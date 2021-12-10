<?php

namespace Aliftech\Kafka\Facades;

use Illuminate\Support\Facades\Facade;
use Aliftech\Kafka\Message\Message;
use Aliftech\Kafka\Support\Testing\Fakes\KafkaFake;

/**
 * @method static \Aliftech\Kafka\Contracts\CanProduceMessages publishOn(string $topic, string $broker = null);
 * @method static \Aliftech\Kafka\Consumers\ConsumerBuilder createConsumer(array $topics = [], string $groupId = null, string $brokers = null);
 * @method static void assertPublished(Message $message = null);
 * @method static void assertPublishedTimes(int $times = 1, Message $message = null);
 * @method static void assertPublishedOn(string $topic, Message $message = null, $callback = null)
 * @method static void assertPublishedOnTimes(string $topic, int $times = 1, Message $message = null, $callback = null)
 * @method static void assertNothingPublished()
 * @mixin \Aliftech\Kafka\Kafka
 *
 * @see \Aliftech\Kafka\Kafka
 */
class Kafka extends Facade
{
    /**
     * Replace the bound instance with a fake.
     *
     * @return \Aliftech\Kafka\Support\Testing\Fakes\KafkaFake
     */
    public static function fake(): KafkaFake
    {
        static::swap($fake = new KafkaFake());

        return $fake;
    }

    public static function getFacadeAccessor(): string
    {
        return \Aliftech\Kafka\Kafka::class;
    }
}
