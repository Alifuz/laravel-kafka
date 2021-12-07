<?php

namespace Aliftech\Kafka\Tests\Fakes;

use Aliftech\Kafka\Commit\Contracts\Sleeper;

class FakeSleeper implements Sleeper
{
    private array $sleeps = [];

    public function sleep(int $timeInMicroseconds): void
    {
        $this->sleeps[] = $timeInMicroseconds;
    }

    public function getSleeps(): array
    {
        return $this->sleeps;
    }
}
