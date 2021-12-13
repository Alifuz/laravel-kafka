<?php

namespace Aliftech\Kafka;

use Closure;

abstract class AbstractHandler
{
    /**
     * Handle the topic message
     *
     * @param $message
     * @return void
     */
    abstract public function handle($message): void;

    /**
     * Middleware to be passed before handling the message
     *
     * @param $message
     * @return void
     */
    public function middleware($message, Closure $next): void
    {
        $next($message);
    }
}
