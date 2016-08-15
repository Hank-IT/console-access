<?php

/**
 * This file contains the ConsoleAccess class.
 * It exposes an api to execute a console command via an adapter.
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Exceptions
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */

namespace MrCrankHank\ConsoleAccess;

use MrCrankHank\ConsoleAccess\Exceptions\MissingCommandException;
use MrCrankHank\ConsoleAccess\Interfaces\ConsoleAccessInterface;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

/**
 * Class ConsoleAccess
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Exceptions
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
class ConsoleAccess implements ConsoleAccessInterface {
    /**
     * Adapter to execute the functions on.
     *
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * Path to the sudo binary
     *
     * @var
     */
    private $sudo = false;

    /**
     * Command to be executed
     *
     * @var
     */
    private $command;

    /**
     * ConsoleAccess constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Prepend sudo to the command.
     *
     * @param string $sudo Path to the sudo binary
     * @return $this
     */
    public function sudo($sudo = '/usr/bin/sudo')
    {
        $this->sudo = $sudo;

        return $this;
    }

    /**
     * Set the command to which should be executed.
     *
     * @param $command
     * @return $this
     */
    public function command($command)
    {
        $this->command = $command;

        return $this;
    }

    /**
     * Exec the command on the adapter.
     * You can pass a closure to capture
     * the live output.
     *
     * @param Closure|null $live
     * @throws MissingCommandException
     */
    public function exec(Closure $live = null)
    {
        if (is_null($this->command)) {
            throw new MissingCommandException('Command is missing');
        }

        if ($this->sudo === false) {
            $this->adapter->run($this->command, $live);
        } else {
            $this->adapter->run($this->sudo . ' ' . $this->command, $live);
        }
    }

    /**
     * Get full output of the
     * executed command
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->adapter->getOutput();
    }

    /**
     * Get exit status of the executed command
     *
     * @return mixed
     */
    public function getExitStatus()
    {
        return $this->adapter->getExitStatus();
    }
}