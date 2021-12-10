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
        $this->offerPublishing();
        $this->registerCommands();
    }

    public function register()
    {
        $this->configure();

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


    /**
     * Setup the configuration for Horizon.
     *
     * @return void
     */
    protected function configure()
    {
        // fallback config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/kafka.php', 'kafka'
        );
    }

    /**
     * Setup the resource publishing groups for Horizon.
     *
     * @return void
     */
    protected function offerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../stubs/KafkaServiceProvider.stub' => app_path('Providers/KafkaServiceProvider.php'),
            ], 'kafka-provider');
            $this->publishes([
                __DIR__.'/../../config/kafka.php' => config_path('kafka.php'),
            ], 'kafka-config');
        }
    }

    /**
     * Register the Horizon Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Aliftech\Kafka\Console\InstallCommand::class,
                \Aliftech\Kafka\Console\KafkaConsumeCommand::class,
                \Aliftech\Kafka\Console\MessageMakeCommand::class,
                \Aliftech\Kafka\Console\TopicMakeCommand::class,
            ]);
        }
    }
}
