<?php

namespace Aliftech\Kafka\Console;

use Illuminate\Console\Command;
use Aliftech\Kafka\Config\Sasl;
use Aliftech\Kafka\Console\KafkaConsumer\Handler;
use Aliftech\Kafka\Console\KafkaConsumer\Options;
use Aliftech\Kafka\Consumers\ConsumerBuilder;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume
            {--topic=* : The topic to listen for messages}
            {--groupId= : The consumer group id}
            {--maxMessage= : The max number of messages that should be handled}';

    protected $description = 'A Kafka Consumer for Laravel.';

    private array $config;

    private array $subscribe;

    public function __construct()
    {
        parent::__construct();

        $this->subscribe = app('kafka.subscribe');

        $this->config = [
            'autoCommit' => config('kafka.auto_commit'),
            'dlqTopic' => config('kafka.dlq_topic'),
            'brokers' => config('kafka.brokers'),
            'groupId' => config('kafka.consumer_group_id'),
            'securityProtocol' => config('kafka.securityProtocol'),
            'sasl' => [
                'mechanisms' => config('kafka.sasl.mechanisms'),
                'username' => config('kafka.sasl.username'),
                'password' => config('kafka.sasl.password'),
            ],
        ];
    }

    /**
     * @throws \Carbon\Exceptions\Exception
     * @throws \RdKafka\Exception
     */
    public function handle()
    {
        // Start the consumers
        $this->comment('Starting the consumer...');
        $this->getConsumer()->consume();
    }

    private function getSubscribers() {
        return $this->subscribe;
    }

    private function getConsumer() {

        $options = new Options($this->options(), $this->config, $this->subscribe);

        $handler = new Handler($this->getSubscribers());

        $consumer = ConsumerBuilder::create(
            brokers: $options->getBrokers(),
            topics: $options->getTopics(),
            groupId: $options->getGroupId()
        )
            ->withDlq($options->getDlqTopic())
            // ->withCommitBatchSize(1)
            // ->withMaxCommitRetries(6)
            ->withMaxMessages($options->getMaxMessage())
            ->withSasl(new Sasl(
                username: $this->config['sasl']['username'],
                password: $this->config['sasl']['password'],
                mechanisms: $this->config['sasl']['mechanisms']
            ))
            ->withSecurityProtocol($options->getSecurityProtocol())
            ->withHandler(function (\Aliftech\Kafka\Contracts\KafkaConsumerMessage $message) use ($handler) {
                $handler->handle($message);
            });

        if ($options->getAutoCommit()) {
            $consumer = $consumer->withAutoCommit();
        }

        return $consumer->build();
    }


}
