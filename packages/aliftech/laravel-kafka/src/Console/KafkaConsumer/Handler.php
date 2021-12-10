<?php

namespace Aliftech\Kafka\Console\KafkaConsumer;

use Illuminate\Support\Facades\Log;

class Handler
{
    private array $subscribe;

    function __construct($subscribe)
    {
        $this->subscribe = $subscribe;
    }

    public function handle(\Aliftech\Kafka\Contracts\KafkaConsumerMessage $message){
        Log::alert((string) $message->getBody());
    }
}
