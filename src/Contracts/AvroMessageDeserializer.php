<?php

namespace Aliftech\Kafka\Contracts;

interface AvroMessageDeserializer extends MessageDeserializer
{
    public function getRegistry(): AvroSchemaRegistry;
}
