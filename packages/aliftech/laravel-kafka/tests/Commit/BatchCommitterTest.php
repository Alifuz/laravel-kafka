<?php

namespace Aliftech\Kafka\Tests\Commit;

use Aliftech\Kafka\Commit\BatchCommitter;
use Aliftech\Kafka\Commit\Contracts\Committer;
use Aliftech\Kafka\MessageCounter;
use Aliftech\Kafka\Tests\LaravelKafkaTestCase;

class BatchCommitterTest extends LaravelKafkaTestCase
{
    public function testShouldCommitMessageOnlyAfterTheBatchSizeIsReached()
    {
        $committer = $this->createMock(Committer::class);
        $committer
            ->expects($this->exactly(2))
            ->method('commitMessage');

        $batchSize = 3;
        $messageCounter = new MessageCounter(42);
        $batchCommitter = new BatchCommitter($committer, $messageCounter, $batchSize);

        for ($i = 0; $i < 7; $i++) {
            $batchCommitter->commitMessage();
        }
    }

    public function testShouldAlwaysCommitDlq()
    {
        $committer = $this->createMock(Committer::class);
        $committer
            ->expects($this->exactly(2))
            ->method('commitDlq');

        $batchSize = 3;

        $messageCounter = new MessageCounter(42);
        $batchCommitter = new BatchCommitter($committer, $messageCounter, $batchSize);

        $batchCommitter->commitDlq();
        $batchCommitter->commitDlq();
    }
}
