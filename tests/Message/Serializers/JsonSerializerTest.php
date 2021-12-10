<?php

namespace Aliftech\Kafka\Tests\Message\Serializers;

use JsonException;
use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Message\Serializers\JsonSerializer;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase as TestCase;

class JsonSerializerTest extends TestCase
{
    public function testSerialize()
    {
        $message = $this->getMockForAbstractClass(KafkaProducerMessage::class);
        $message->expects($this->once())->method('getBody')->willReturn(['name' => 'foo']);
        $message->expects($this->once())->method('withBody')->with('{"name":"foo"}')->willReturn($message);

        $serializer = $this->getMockForAbstractClass(JsonSerializer::class);

        $this->assertSame($message, $serializer->serialize($message));
    }

    public function testSerializeThrowsException(): void
    {
        $message = $this->getMockForAbstractClass(KafkaProducerMessage::class);
        $message->expects($this->once())->method('getBody')->willReturn(chr(255));

        $serializer = $this->getMockForAbstractClass(JsonSerializer::class);

        $this->expectException(JsonException::class);

        $serializer->serialize($message);
    }
}
