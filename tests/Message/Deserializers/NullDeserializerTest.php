<?php

namespace Aliftech\Kafka\Tests\Message\Deserializers;

use Aliftech\Kafka\Contracts\KafkaConsumerMessage;
use Aliftech\Kafka\Message\Deserializers\NullDeserializer;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;

class NullDeserializerTest extends LaravelKafkaTestCase
{
    public function testDeserialize(): void
    {
        $message = $this->getMockForAbstractClass(KafkaConsumerMessage::class);

        $this->assertSame($message, (new NullDeserializer())->deserialize($message));
    }
}
