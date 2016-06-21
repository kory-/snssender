<?php namespace Socialgrid\SnsSender;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {

        $this->handleConfigs();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Config
        $this->mergeConfigFrom(__DIR__ . '/../config/snssender.php', 'snssender');
        $this->app['snssender'] = $this->app->share(function($app) {
            return new SnsSender;
        });

    }

    private function handleConfigs() {

        $configPath = __DIR__ . '/../config/snssender.php';

        $this->publishes([$configPath => config_path('snssender.php')]);

        $this->mergeConfigFrom($configPath, 'snssender');
    }
}
