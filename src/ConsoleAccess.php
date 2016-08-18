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
     * Store closure which will be
     * executed before the command
     *
     * @var
     */
    private $pre;

    /**
     * Store closure which will be
     * executed after the command
     *
     * @var
     */
    private $post;

    /**
     * Escape the command using escapeshellcmd?
     *
     * @var bool
     */
    public $escaped = true;

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

        // prepend sudo if enabled
        if ($this->sudo) {
            $this->command = $this->sudo . ' ' . $this->command;
        }

        // escape the command if enabled
        if ($this->escaped) {
            $this->command = escapeshellcmd($this->command);
        }

        if (!is_null($this->pre)) {
            call_user_func($this->pre, $this->command);
        }

        $this->adapter->run($this->command, $live);

        if (!is_null($this->post)) {
            call_user_func_array($this->post, [$this->command, $this->getExitStatus()]);
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

    /**
     * Return the given command.
     *
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * By default the command is escaped.
     * Use this function if you want to disable it.
     *
     * @return $this
     */
    public function notEscaped() {
        $this->escaped = false;
        return $this;
    }

    /**
     * Set a function which will be executed directly
     * before the command is executed. You can write
     * something into an audit log for example.
     * The function will receive the command as first
     * parameter.
     *
     * @param Closure $function
     */
    public function setPreExec(Closure $function)
    {
        $this->pre = $function;
    }

    /**
     * Set a function which will be executed directly
     * after the command was executed. You can write
     * something into an audit log for example.
     * The function will receive the command as first
     * parameter and the exit status as second one.
     *
     * @param Closure $function
     */
    public function setPostExec(Closure $function)
    {
        $this->post = $function;
    }
}