<?php

/**
 * This file contains the ConsoleAccessServiceProvider class
 * It makes the functionality available to laravel.
 *
 * PHP version 5.6
 *
 * @category Console
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
namespace MrCrankHank\ConsoleAccess;

use Illuminate\Support\ServiceProvider;
use MrCrankHank\ConsoleAccess\Adapters\LocalAdapter;
use MrCrankHank\ConsoleAccess\Adapters\SshAdapter;

/**
 * Class ConsoleAccessServiceProvider.
 *
 * @category Console
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
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

        $this->app->bind(SshAdapter::class, function ($app, $parameters) {
            return new SshAdapter($parameters['host'], $parameters['user'], $parameters['publicKey']);
        });
    }
}
