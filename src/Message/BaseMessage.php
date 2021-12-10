<?php

namespace Aliftech\Kafka\Message;

use Aliftech\Kafka\Config\Sasl;
use Aliftech\Kafka\Message\Serializers\JsonSerializer;
use Aliftech\Kafka\Producers\ProducerBuilder;
use Exception;
use ReflectionClass;
use ReflectionProperty;

// topic
// partition
// brokers

class BaseMessage
{

    protected ?string $topic_class = null;
    private ?string $_brokers = null;
    private array $_headers = [];
    private ?string $_message_key = null;

    public static function create(...$args) {
        return new static(...$args);
    }

    public function publish(): bool {
        return $this->configureProducer()->send(); // failed_messages
    }

    public function setHeaders(array $headers): void {
        $this->_headers = $headers;
    }

    public function setBrokers(string $brokers): void {
        $this->_brokers = $brokers;
    }

    public function setKey(array $key): void {
        $this->_message_key = $key;
    }

    public function setTopic(string $topic): void {
        $this->topic_class = $topic;
    }


    private function configureProducer() {
        return $this->validate()
            ->getProducer()
            ->withSasl($this->getSaslConfig())
            ->withKafkaKey($this->getMessageKey())
            ->withHeaders($this->getMessageHeaders())
            ->usingSerializer(new JsonSerializer)
            ->withMessage($this->getMessage());
    }

    private function getProducer() {
        return new ProducerBuilder(
            topic: $this->getTopicKey(),
            broker: $this->getBrokersList()
        );
    }

    private function getBrokersList() {
        return $this->_brokers ?? config('kafka.brokers');
    }

    private function getMessage() {
        return new Message(
            headers: $this->getMessageHeaders(),
            body: $this->getMessageBody(),
            key: $this->getMessageKey()
        );
    }

    private function getTopicKey(): string {
        return !@$this->topic_class ? null : @get_class_vars($this->topic_class)['topic_key'];
    }

    private function getMessageKey(): string {
        return $this->_message_key;
    }

    private function getMessageHeaders(): array {
        return $this->_headers;
    }

    private function getMessageBody(): array {

        $message = new ReflectionClass($this);

        $body = [];
        foreach ($message->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
            $prop = $prop->getName();
            $body[$prop] = $this->{$prop};
        }

        return $body;
    }

    private function getSaslConfig() {
        return new Sasl(config('kafka.sasl.username'), config('kafka.sasl.password'), config('kafka.sasl.mechanisms'), config('kafka.security_protocol'));
    }

    private function validate() {

        // Validate topicKey
        if (!$this->getTopicKey()) {
            throw new Exception('The topic is missing error! Please provide the topic');
        }

        // Validate brokers
        if (!$this->getBrokersList()) {
            throw new Exception('The brokers are missing error! Please provide the brokers');
        }
        return $this;
    }
}
