<?php

/**
 * This file contains the LocalAdapter class.
 * It implements the access to the console of
 * the local server.
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
 * Class LocalAdapter
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Adapters
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
class LocalAdapter implements AdapterInterface {
    /**
     * @var string
     */
    private $exitStatus;

    /**
     * @var string
     */
    private $output;

    /**
     * Run a command.
     *
     * @param string       $command Command which should be run
     * @param Closure|null $live    Closure to capture the live output of the command
     */
    public function run($command, Closure $live = null)
    {
        while (@ ob_end_flush());

        $run = popen($command . ' 2>&1', 'r');

        if (!is_null($live)) {
            while (!feof($run)) {
                $line = fread($run, 4096);

                call_user_func($live, $line);

                @flush();
            }
        }

        $this->output = fread($run, 8192);

        $this->exitStatus = pclose($run);
    }

    /**
     * Return the output of the last command.
     * Does not work well with long running commands.
     * You should capture the live output instead.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return the exist status of the last command
     *
     * @return mixed
     */
    public function getExitStatus()
    {
        return $this->exitStatus;
    }
}