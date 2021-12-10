<?php

namespace Aliftech\Kafka\Tests\Commit;

use Aliftech\Kafka\Commit\BatchCommitter;
use Aliftech\Kafka\Commit\CommitterFactory;
use Aliftech\Kafka\Commit\KafkaCommitter;
use Aliftech\Kafka\Commit\NativeSleeper;
use Aliftech\Kafka\Commit\RetryableCommitter;
use Aliftech\Kafka\Commit\VoidCommitter;
use Aliftech\Kafka\Config\Config;
use Aliftech\Kafka\Contracts\Consumer;
use Aliftech\Kafka\MessageCounter;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;
use RdKafka\KafkaConsumer;

class CommitterFactoryTest extends LaravelKafkaTestCase
{
    public function testShouldBuildARetryableBatchCommitterWhenAutoCommitIsDisable(): void
    {
        $config = new Config(
            broker: 'broker',
            topics: ['topic'],
            securityProtocol: 'security',
            commit: 1,
            groupId: 'group',
            consumer: $this->createMock(Consumer::class),
            sasl: null,
            dlq: null,
            maxMessages: -1,
            maxCommitRetries: 6,
            autoCommit: false
        );

        $consumer = $this->createMock(KafkaConsumer::class);

        $messageCounter = new MessageCounter(6);

        $factory = new CommitterFactory($messageCounter);

        $committer = $factory->make($consumer, $config);

        $expectedCommitter = new BatchCommitter(
            new RetryableCommitter(
                new KafkaCommitter(
                    $consumer
                ),
                new NativeSleeper(),
                $config->getMaxCommitRetries()
            ),
            $messageCounter,
            $config->getCommit()
        );

        $this->assertEquals($expectedCommitter, $committer);
    }

    public function testShouldBuildAVoidCommitterWhenAutoCommitIsEnabled(): void
    {
        $config = new Config(
            broker: 'broker',
            topics: ['topic'],
            securityProtocol: 'security',
            commit: 1,
            groupId: 'group',
            consumer: $this->createMock(Consumer::class),
            sasl: null,
            dlq: null,
            maxMessages: 6,
            maxCommitRetries: 6,
            autoCommit: true
        );

        $consumer = $this->createMock(KafkaConsumer::class);

        $messageCounter = new MessageCounter(6);

        $factory = new CommitterFactory($messageCounter);

        $committer = $factory->make($consumer, $config);

        $this->assertInstanceOf(VoidCommitter::class, $committer);
    }
}
