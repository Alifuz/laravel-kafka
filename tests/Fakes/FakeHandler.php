<?php

namespace Aliftech\Kafka\Tests\Fakes;

use Aliftech\Kafka\Contracts\Consumer;
use Aliftech\Kafka\Contracts\KafkaConsumerMessage;

class FakeHandler extends Consumer
{
    private ?KafkaConsumerMessage $lastMessage = null;

    public function lastMessage(): ?KafkaConsumerMessage
    {
        return $this->lastMessage;
    }

    public function handle(KafkaConsumerMessage $message): void
    {
        $this->lastMessage = $message;
    }
}
