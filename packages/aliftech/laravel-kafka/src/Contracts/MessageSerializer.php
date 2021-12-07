<?php

namespace Aliftech\Kafka\Contracts;

interface MessageSerializer
{
    public function serialize(KafkaProducerMessage $message): KafkaProducerMessage;
}
