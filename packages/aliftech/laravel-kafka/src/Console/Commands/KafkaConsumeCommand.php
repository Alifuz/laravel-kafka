<?php

namespace Aliftech\Kafka\Console\Commands;

use Illuminate\Console\Command;
use Aliftech\Kafka\Config\Config;
use Aliftech\Kafka\Config\Sasl;
use Aliftech\Kafka\Console\Commands\KafkaConsumer\Options;
use Aliftech\Kafka\Consumers\Consumer;

class KafkaConsumeCommand extends Command
{
    protected $signature = 'kafka:consume
            {--topic=* : The topic to listen for messages}
            {--consumer= : The consumer which will consume messages in the specified topic}
            {--groupId= : The consumer group id}
            {--commit=1}
            {--dlq= : The Dead Letter Queue}
            {--maxMessage= : The max number of messages that should be handled}';
    protected $description = 'A Kafka Consumer for Laravel.';

    private array $config;

    public function __construct()
    {
        parent::__construct();

        $this->config = [
            'broker' => config('kafka.brokers'),
            'groupId' => config('kafka.group_id'),
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
        $options = new Options($this->options(), $this->config);

        $consumer = $options->getConsumer();

        $config = new Config(
            broker: $this->config['broker'],
            topics: $options->getTopics(),
            securityProtocol: $this->config['securityProtocol'],
            commit: $options->getCommit(),
            groupId: $options->getGroupId(),
            consumer: new $consumer(),
            sasl: new Sasl(
                username: $this->config['sasl']['username'],
                password: $this->config['sasl']['password'],
                mechanisms: $this->config['sasl']['mechanisms']
            ),
            dlq: $options->getDlq(),
            maxMessages: $options->getMaxMessage()
        );

        (new Consumer($config))->consume();
    }
}
