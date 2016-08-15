<?php

/**
 * This file contains the ConsoleAccessServiceProvider class
 * It makes the functionality available to laravel
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */

namespace MrCrankHank\ConsoleAccess;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConsoleAccessServiceProvider
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
class ConsoleAccessServiceProvider extends ServiceProvider
{
    /**
     * All commands in this array will be registered with laravel
     *
     * @var array
     */
    protected $commands = [];

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
        $this->app->bind(ConsoleAccess::class, function($app, $parameters) {
            return new ConsoleAccess($parameters['adapter']);
        });

        $this->commands($this->commands);
    }
}
