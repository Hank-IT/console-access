<?php

namespace HankIT\ConsoleAccess;

use HankIT\ConsoleAccess\Adapters\LocalAdapter;
use HankIT\ConsoleAccess\Adapters\SshAdapter\Adapter;
use Illuminate\Support\ServiceProvider;

class ConsoleAccessServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ConsoleAccess::class, function ($app, $parameters) {
            return new ConsoleAccess($parameters['adapter']);
        });

        $this->app->bind(LocalAdapter::class, function ($app, $parameters) {
            return new LocalAdapter;
        });

        $this->app->bind(Adapter::class, function ($app, $parameters) {
            return new Adapter($parameters['host'], $parameters['user'], $parameters['publicKey']);
        });
    }
}
