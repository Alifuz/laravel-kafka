<?php

namespace Aliftech\Kafka\Providers;

use Illuminate\Support\ServiceProvider;
use Aliftech\Kafka\Contracts\KafkaConsumerMessage;
use Aliftech\Kafka\Contracts\KafkaProducerMessage;
use Aliftech\Kafka\Contracts\MessageDeserializer;
use Aliftech\Kafka\Contracts\MessageSerializer;
use Aliftech\Kafka\Message\ConsumedMessage;
use Aliftech\Kafka\Message\Deserializers\JsonDeserializer;
use Aliftech\Kafka\Message\Message;
use Aliftech\Kafka\Message\Serializers\JsonSerializer;

class LaravelKafkaServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->publishesConfiguration();
    }

    public function register()
    {
        $this->app->bind(MessageSerializer::class, function () {
            return new JsonSerializer();
        });

        $this->app->bind(MessageDeserializer::class, function () {
            return new JsonDeserializer();
        });

        $this->app->bind(KafkaProducerMessage::class, function () {
            return new Message('');
        });

        $this->app->bind(KafkaConsumerMessage::class, ConsumedMessage::class);
    }

    private function publishesConfiguration()
    {
        $this->publishes([
            __DIR__."/../../config/kafka.php" => config_path('kafka.php'),
        ], 'laravel-kafka-config');
    }
}
