<?php

namespace Aliftech\Kafka\Message\Serializers;

use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Contracts\MessageSerializer;

class NullSerializer implements MessageSerializer
{
    public function serialize(KafkaProducerMessage $message): KafkaProducerMessage
    {
        return $message;
    }
}
