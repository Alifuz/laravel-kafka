<?php

namespace Aliftech\Kafka\Tests\Fakes;

use Aliftech\Kafka\Contracts\KafkaConsumerMessage;

class FakeConsumer
{
    private KafkaConsumerMessage $message;

    public function __invoke(KafkaConsumerMessage $message)
    {
        $this->message = $message;
    }

    public function getMessage(): KafkaConsumerMessage
    {
        return $this->message;
    }
}
