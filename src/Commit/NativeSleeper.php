<?php

namespace Aliftech\Kafka\Commit;

use Aliftech\Kafka\Commit\Contracts\Sleeper;

class NativeSleeper implements Sleeper
{
    public function sleep(int $timeInMicroseconds): void
    {
        usleep($timeInMicroseconds);
    }
}
