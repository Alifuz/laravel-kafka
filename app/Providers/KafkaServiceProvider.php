<?php

namespace App\Providers;

use Aliftech\Kafka\Providers\KafkaApplicationServiceProvider;
use App\Kafka\Topics\FirstTopic;

class KafkaServiceProvider extends KafkaApplicationServiceProvider
{
    /**
     * The topic consumer mappings for the application.
     *
     * @var array
     */
    protected $subscribe = [
        FirstTopic::class => [
            HandleTopic::class,
            SendNotification::class,
        ],
    ];
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
