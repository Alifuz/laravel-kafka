<?php

namespace Aliftech\Kafka\Commit;

use Aliftech\Kafka\Commit\Contracts\Committer;
use RdKafka\KafkaConsumer;

class KafkaCommitter implements Committer
{
    public function __construct(private KafkaConsumer $consumer)
    {
    }

    /**
     * @throws \RdKafka\Exception
     */
    public function commitMessage(): void
    {
        $this->consumer->commit();
    }

    /**
     * @throws \RdKafka\Exception
     */
    public function commitDlq(): void
    {
        $this->consumer->commit();
    }
}
