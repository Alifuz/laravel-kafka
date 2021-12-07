<?php

namespace Aliftech\Kafka\Commit;

use JetBrains\PhpStorm\Pure;
use Aliftech\Kafka\Commit\Contracts\Committer;
use Aliftech\Kafka\Commit\Contracts\Sleeper;
use Aliftech\Kafka\Retryable;

class RetryableCommitter implements Committer
{
    private const RETRYABLE_ERRORS = [
        RD_KAFKA_RESP_ERR_REQUEST_TIMED_OUT,
    ];

    private Committer $committer;
    private Retryable $retryable;

    /**
     * @param Committer $committer
     * @param Sleeper $sleeper
     * @param int $maximumRetries
     */
    #[Pure]
    public function __construct(Committer $committer, Sleeper $sleeper, int $maximumRetries = 6)
    {
        $this->committer = $committer;
        $this->retryable = new Retryable($sleeper, $maximumRetries, self::RETRYABLE_ERRORS);
    }

    /**
     * @throws \Carbon\Exceptions\Exception
     */
    public function commitMessage(): void
    {
        $this->retryable->retry(fn () => $this->committer->commitMessage());
    }

    /**
     * @throws \Carbon\Exceptions\Exception
     */
    public function commitDlq(): void
    {
        $this->retryable->retry(fn () => $this->committer->commitDlq());
    }
}
