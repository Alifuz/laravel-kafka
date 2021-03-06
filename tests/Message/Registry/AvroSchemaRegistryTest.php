<?php

namespace Aliftech\Kafka\Tests\Message\Registry;

use AvroSchema;
use FlixTech\SchemaRegistryApi\Registry;
use Aliftech\Kafka\Contracts\KafkaAvroSchemaRegistry;
use Aliftech\Kafka\Exceptions\SchemaRegistryException;
use Aliftech\Kafka\Message\Registry\AvroSchemaRegistry;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;
use ReflectionProperty;

class AvroSchemaRegistryTest extends LaravelKafkaTestCase
{
    /**
     * @throws \ReflectionException
     */
    public function testAddBodySchemaMappingForTopic()
    {
        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addBodySchemaMappingForTopic('test', $schema);

        $reflectionProperty = new ReflectionProperty($registry, 'schemaMapping');
        $reflectionProperty->setAccessible(true);

        $schemaMapping = $reflectionProperty->getValue($registry);

        $this->assertArrayHasKey(AvroSchemaRegistry::BODY_IDX, $schemaMapping);
        $this->assertArrayHasKey('test', $schemaMapping[AvroSchemaRegistry::BODY_IDX]);
        $this->assertSame($schema, $schemaMapping[AvroSchemaRegistry::BODY_IDX]['test']);
    }

    public function testAddKeySchemaMappingForTopic()
    {
        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addKeySchemaMappingForTopic('test2', $schema);

        $reflectionProperty = new ReflectionProperty($registry, 'schemaMapping');
        $reflectionProperty->setAccessible(true);

        $schemaMapping = $reflectionProperty->getValue($registry);

        $this->assertArrayHasKey(AvroSchemaRegistry::KEY_IDX, $schemaMapping);
        $this->assertArrayHasKey('test2', $schemaMapping[AvroSchemaRegistry::KEY_IDX]);
        $this->assertSame($schema, $schemaMapping[AvroSchemaRegistry::KEY_IDX]['test2']);
    }

    public function testHasBodySchemaMappingForTopic()
    {
        $flixRegistry = $this->getMockForAbstractClass(Registry::class);
        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);
        $registry->addBodySchemaMappingForTopic('test', $schema);

        $this->assertTrue($registry->hasBodySchemaForTopic('test'));
        $this->assertFalse($registry->hasBodySchemaForTopic('test2'));
    }

    public function testHasKeySchemaMappingForTopic()
    {
        $flixRegistry = $this->getMockForAbstractClass(Registry::class);
        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);
        $registry->addKeySchemaMappingForTopic('test', $schema);

        $this->assertTrue($registry->hasKeySchemaForTopic('test'));
        $this->assertFalse($registry->hasKeySchemaForTopic('test2'));
    }

    public function testGetBodySchemaForTopicWithNoMapping()
    {
        $this->expectException(SchemaRegistryException::class);
        $this->expectExceptionMessage(
            sprintf(
                SchemaRegistryException::SCHEMA_MAPPING_NOT_FOUND,
                'test',
                AvroSchemaRegistry::BODY_IDX
            )
        );

        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->getBodySchemaForTopic('test');
    }

    public function testGetBodySchemaForTopicWithMappingWithDefinition()
    {
        $definition = $this->getMockBuilder(AvroSchema::class)->disableOriginalConstructor()->getMock();

        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);
        $schema->expects($this->once())->method('getDefinition')->willReturn($definition);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addBodySchemaMappingForTopic('test', $schema);

        $this->assertSame($schema, $registry->getBodySchemaForTopic('test'));
    }

    public function testGetKeySchemaForTopicWithMappingWithDefinition()
    {
        $definition = $this->getMockBuilder(AvroSchema::class)->disableOriginalConstructor()->getMock();

        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);
        $schema->expects($this->once())->method('getDefinition')->willReturn($definition);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addKeySchemaMappingForTopic('test2', $schema);

        $this->assertSame($schema, $registry->getKeySchemaForTopic('test2'));
    }

    public function testGetBodySchemaForTopicWithMappingWithoutDefinitionLatest()
    {
        $definition = $this->getMockBuilder(AvroSchema::class)->disableOriginalConstructor()->getMock();

        $flixRegistry = $this->getMockForAbstractClass(Registry::class);
        $flixRegistry->expects($this->once())->method('latestVersion')->with('test-schema')->willReturn($definition);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);
        $schema->expects($this->once())->method('getDefinition')->willReturn(null);
        $schema->expects($this->once())->method('getVersion')->willReturn(KafkaAvroSchemaRegistry::LATEST_VERSION);
        $schema->expects($this->once())->method('getName')->willReturn('test-schema');
        $schema->expects($this->once())->method('setDefinition')->with($definition);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addBodySchemaMappingForTopic('test', $schema);

        $registry->getBodySchemaForTopic('test');
    }

    public function testGetBodySchemaForTopicWithMappingWithoutDefinitionVersion()
    {
        $definition = $this->getMockBuilder(AvroSchema::class)->disableOriginalConstructor()->getMock();

        $flixRegistry = $this->getMockForAbstractClass(Registry::class);
        $flixRegistry->expects($this->once())->method('schemaForSubjectAndVersion')->with('test-schema', 1)->willReturn($definition);

        $schema = $this->getMockForAbstractClass(KafkaAvroSchemaRegistry::class);
        $schema->expects($this->once())->method('getDefinition')->willReturn(null);
        $schema->expects($this->exactly(2))->method('getVersion')->willReturn(1);
        $schema->expects($this->once())->method('getName')->willReturn('test-schema');
        $schema->expects($this->once())->method('setDefinition')->with($definition);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $registry->addBodySchemaMappingForTopic('test', $schema);

        $registry->getBodySchemaForTopic('test');
    }

    public function testGetTopicSchemaMapping()
    {
        $flixRegistry = $this->getMockForAbstractClass(Registry::class);

        $registry = new AvroSchemaRegistry($flixRegistry);

        $this->assertIsArray($registry->getTopicSchemaMapping());
    }
}
