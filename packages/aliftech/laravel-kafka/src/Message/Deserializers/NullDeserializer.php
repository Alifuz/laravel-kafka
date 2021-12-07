<?php

namespace Aliftech\Kafka\Message\Deserializers;

use Aliftech\Kafka\Contracts\KafkaConsumerMessage;
use Aliftech\Kafka\Contracts\MessageDeserializer;

class NullDeserializer implements MessageDeserializer
{
    public function deserialize(KafkaConsumerMessage $message): KafkaConsumerMessage
    {
        return $message;
    }
}
