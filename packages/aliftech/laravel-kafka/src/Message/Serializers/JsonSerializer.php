<?php

namespace Aliftech\Kafka\Message\Serializers;

use JsonException;
use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Contracts\MessageSerializer;

class JsonSerializer implements MessageSerializer
{
    /**
     * @param KafkaProducerMessage $message
     * @return KafkaProducerMessage
     * @throws JsonException
     */
    public function serialize(KafkaProducerMessage $message): KafkaProducerMessage
    {
        $body = json_encode($message->getBody(), JSON_THROW_ON_ERROR);

        return $message->withBody($body);
    }
}
