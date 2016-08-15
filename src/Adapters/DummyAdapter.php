<?php

/**
 * This file contains the DummyAdapter class.
 * It provides a way to just don't execute any
 * commands and get no errors for it. Useful
 * for demos, which shouldn't change the config.
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Adapters
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */

namespace MrCrankHank\ConsoleAccess\Adapters;

use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

/**
 * Class DummyAdapter
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Adapters
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
class DummyAdapter implements AdapterInterface {
    /**
     * Dummy method
     *
     * @param              $command
     * @param Closure|null $live
     * @return null
     */
    public function run($command, Closure $live = null)
    {
        return null;
    }

    /**
     * Dummy method
     *
     * @return null
     */
    public function getOutput()
    {
        return null;
    }

    /**
     * Dummy method
     *
     * @return null
     */
    public function getExitStatus()
    {
        return null;
    }
}