<?php

namespace Aliftech\Kafka\Commit;

use Aliftech\Kafka\Commit\Contracts\Committer;
use Aliftech\Kafka\Config\Config;
use Aliftech\Kafka\MessageCounter;
use RdKafka\KafkaConsumer;

class CommitterFactory
{
    public function __construct(private MessageCounter $messageCounter)
    {
    }

    public function make(KafkaConsumer $kafkaConsumer, Config $config): Committer
    {
        if ($config->isAutoCommit()) {
            return new VoidCommitter();
        }

        return new BatchCommitter(
            new RetryableCommitter(
                new KafkaCommitter(
                    $kafkaConsumer
                ),
                new NativeSleeper(),
                $config->getMaxCommitRetries()
            ),
            $this->messageCounter,
            $config->getCommit()
        );
    }
}
