<?php

namespace Aliftech\Kafka\Contracts;

interface AvroMessageSerializer extends MessageSerializer
{
    public function getRegistry(): AvroSchemaRegistry;
}
