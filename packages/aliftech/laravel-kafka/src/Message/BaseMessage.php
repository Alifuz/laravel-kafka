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

    protected array $_brokers = [];
    protected array $_headers = [];
    protected ?string $_topic = null;
    protected ?string $_message_key = null;

    public static function create(...$args) {
        return new (get_called_class())(...$args);
    }

    public function publish() {
        return $this->configureProducer()->send(); // failed_messages
    }


    private function configureProducer() {
        return $this->validate()
            ->getProducer()
            ->withSasl($this->getSaslConfig())
            ->withKafkaKey($this->getMessageKey())
            ->withHeaders($this->getHeaders())
            ->usingSerializer(new JsonSerializer)
            ->withMessage((new Message())->withBody($this->getBody()));
    }

    private function getProducer() {
        return new ProducerBuilder(
            topic: $this->getTopicKey(),
            broker: $this->getBrokersList()
        );
    }

    private function getBrokersList() {
        return !count($this->_brokers) ?
            config('kafka.brokers') : implode(',', $this->brokers);
    }

    private function getTopicKey(): string {
        return @$this->_topic ?: @get_class_vars($this->_topic)['topic_key'];
    }

    private function getMessageKey(): string {
        return $this->_message_key;
    }

    private function getHeaders(): array {
        return $this->_headers;
    }

    private function getBody(): array {
        return get_object_vars($this);
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
