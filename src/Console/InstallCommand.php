<?php

namespace Aliftech\Kafka\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the Kafka resources';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->comment('Publishing Kafka Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'kafka-provider']);

        $this->comment('Publishing Kafka Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'kafka-config']);

        $this->registerKafkaServiceProvider();

        $this->info('Kafka scaffolding installed successfully.');
    }

    /**
     * Register the Kafka service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerKafkaServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace.'\\Providers\\KafkaServiceProvider::class')) {
            return;
        }

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\EventServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\KafkaServiceProvider::class,".PHP_EOL,
            $appConfig
        ));

        file_put_contents(app_path('Providers/KafkaServiceProvider.php'), str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents(app_path('Providers/KafkaServiceProvider.php'))
        ));
    }
}
