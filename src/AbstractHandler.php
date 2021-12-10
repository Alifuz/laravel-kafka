<?php

namespace Aliftech\Kafka;

use Closure;

abstract class AbstractHandler
{
    abstract public function handle($message): void;

    public function middleware($message, Closure $next): Closure
    {
        return $next($message);
    }
}
