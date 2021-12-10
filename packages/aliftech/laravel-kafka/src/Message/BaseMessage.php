<?php

namespace Aliftech\Kafka\Message;

use Aliftech\Kafka\Config\Sasl;
use Aliftech\Kafka\Message\Serializers\JsonSerializer;
use Aliftech\Kafka\Producers\ProducerBuilder;
use Exception;
use Illuminate\Queue\SerializesModels;

// topic
// partition
// brokers

class BaseMessage
{
    use SerializesModels;

    protected ?string $_brokers = null;
    protected array $_headers = [];
    protected ?string $_topic = null;
    protected ?string $_message_key = null;

    public static function create(...$args) {
        return new static(...$args);
    }

    public function publish() {
        // dd($this->getMessageBody());
        return $this->configureProducer()->send(); // failed_messages
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
        return !@$this->_topic ? null : @get_class_vars($this->_topic)['topic_key'];
    }

    private function getMessageKey(): string {
        return $this->_message_key;
    }

    private function getMessageHeaders(): array {
        return $this->_headers;
    }

    private function getMessageBody(): array {
        return get_class_vars(get_class($this));
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
