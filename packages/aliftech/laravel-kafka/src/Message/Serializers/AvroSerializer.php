<?php

namespace Aliftech\Kafka\Message\Serializers;

use AvroSchema;
use FlixTech\AvroSerializer\Objects\RecordSerializer;
use Aliftech\Kafka\Contracts\AvroMessageSerializer;
use Aliftech\Kafka\Contracts\AvroSchemaRegistry;
use Aliftech\Kafka\Contracts\KafkaAvroSchemaRegistry;
use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Exceptions\Encoders\AvroEncoderException;

class AvroSerializer implements AvroMessageSerializer
{
    public function __construct(
        private AvroSchemaRegistry $registry,
        private RecordSerializer   $recordSerializer
    ) {
    }

    public function getRegistry(): AvroSchemaRegistry
    {
        return $this->registry;
    }

    public function serialize(KafkaProducerMessage $message): KafkaProducerMessage
    {
        $message = $this->encodeBody($message);

        return $this->encodeKey($message);
    }

    private function encodeBody(KafkaProducerMessage $producerMessage): KafkaProducerMessage
    {
        $topicName = $producerMessage->getTopicName();
        $body = $producerMessage->getBody();

        if (null === $body) {
            return $producerMessage;
        }

        if (false === $this->registry->hasBodySchemaForTopic($topicName)) {
            return $producerMessage;
        }

        $avroSchema = $this->registry->getBodySchemaForTopic($topicName);

        $encodedBody = $this->recordSerializer->encodeRecord(
            $avroSchema->getName(),
            $this->getAvroSchemaDefinition($avroSchema),
            $body
        );

        return $producerMessage->withBody($encodedBody);
    }

    private function encodeKey(KafkaProducerMessage $producerMessage): KafkaProducerMessage
    {
        $topicName = $producerMessage->getTopicName();
        $key = $producerMessage->getKey();

        if (null === $key) {
            return $producerMessage;
        }

        if (false === $this->registry->hasKeySchemaForTopic($topicName)) {
            return $producerMessage;
        }

        $avroSchema = $this->registry->getKeySchemaForTopic($topicName);

        $encodedKey = $this->recordSerializer->encodeRecord(
            $avroSchema->getName(),
            $this->getAvroSchemaDefinition($avroSchema),
            $key
        );

        return $producerMessage->withKey($encodedKey);
    }

    private function getAvroSchemaDefinition(KafkaAvroSchemaRegistry $avroSchema): AvroSchema
    {
        $schemaDefinition = $avroSchema->getDefinition();

        if (null === $schemaDefinition) {
            throw new AvroEncoderException(
                sprintf(
                    AvroEncoderException::UNABLE_TO_LOAD_DEFINITION_MESSAGE,
                    $avroSchema->getName()
                )
            );
        }

        return $schemaDefinition;
    }
}
