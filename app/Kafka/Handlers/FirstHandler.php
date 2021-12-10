<?php

namespace App\Kafka\Handlers;

use Aliftech\Kafka\AbstractHandler;

class FirstHandler extends AbstractHandler
{
    /**
     * Handle the topic message.
     *
     * @param $message
     * @return void
     */
    public function handle($message): void
    {
        dd($message);
    }
}
