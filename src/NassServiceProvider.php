<?php

namespace Nass;

use Illuminate\Support\ServiceProvider;

class NassServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/nass.php', 'nass');

        $this->app->singleton(Nass::class, function ($app) {
            return new Nass(
                config('nass.base_url')
            );
        });

        $this->app->alias(Nass::class, 'nass');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/nass.php' => config_path('nass.php'),
            ], 'nass-config');
        }
    }
}
