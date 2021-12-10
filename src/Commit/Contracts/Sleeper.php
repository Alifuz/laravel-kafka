<?php

namespace Aliftech\Kafka\Commit\Contracts;

interface Sleeper
{
    public function sleep(int $timeInMicroseconds): void;
}
