<?php

namespace Aliftech\Kafka\Tests\Message\Serializers;

use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Message\Serializers\NullSerializer;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;

class NullSerializerTest extends LaravelKafkaTestCase
{
    public function testSerializer(): void
    {
        $message = $this->getMockForAbstractClass(KafkaProducerMessage::class);

        $this->assertSame($message, (new NullSerializer())->serialize($message));
    }
}
