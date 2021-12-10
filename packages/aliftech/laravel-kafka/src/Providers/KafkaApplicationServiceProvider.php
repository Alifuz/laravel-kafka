<?php

namespace Aliftech\Kafka\Providers;

use Illuminate\Support\ServiceProvider;

class KafkaApplicationServiceProvider extends ServiceProvider
{
    /**
     * The topic consumer mappings for the application.
     *
     * @var array
     */
    protected $subscribe = [];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('kafka.subscribe', fn () => $this->getSubscribers());
    }

    /**
     * Boot any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Get the discovered events and listeners for the application.
     *
     * @return array
     */
    public function getSubscribers()
    {
        return $this->subscribe;
    }
}
