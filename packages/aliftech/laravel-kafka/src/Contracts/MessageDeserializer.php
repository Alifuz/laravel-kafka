<?php

namespace Aliftech\Kafka\Contracts;

interface MessageDeserializer
{
    public function deserialize(KafkaConsumerMessage $message): KafkaConsumerMessage;
}
