<?php

/**
 * This file contains the ConsoleAccess class.
 * It exposes an api to execute a console command via an adapter.
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

use MrCrankHank\ConsoleAccess\Exceptions\MissingCommandException;
use MrCrankHank\ConsoleAccess\Interfaces\ConsoleAccessInterface;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

/**
 * Class ConsoleAccess
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess
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
     * Save the params
     *
     * @var array
     */
    private $params = [];

    /**
     * Path to the bin, which
     * should be executed.
     *
     * @var string
     */
    private $bin;

    /**
     * Unix timestamp of the start
     * of the command exec
     *
     * @var integer
     */
    private $start;

    /**
     * Unix timestamp of the end
     * of the command exec
     *
     * @var integer
     */
    private $end;

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
     * Give a bin to be executed.
     * You may append parameters using
     * the param() method
     *
     * @param $bin
     * @param $escape boolean
     *
     * @return $this
     */
    public function bin($bin, $escape = true)
    {
        if ($escape) {
            // prevent multiple commands from
            // being passed by escapine the value
            $bin = escapeshellcmd($bin);
        }

        $this->bin = $bin;

        return $this;
    }

    /**
     * Append parameters
     *
     * @param $param
     * @param $escape boolean
     *
     * @return $this
     */
    public function param($param, $escape = true)
    {
        if ($escape) {
            // prevent multiple parameters from being
            // passed by escaping the value
            $param = escapeshellarg($param);
        }

        $this->params[] = $param;

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
        if (is_null($this->bin)) {
            throw new MissingCommandException('Command is missing');
        }

        if (!empty($this->params)) {
            // add whitespace to the end
            $this->bin .= ' ';
        }

        // prepend sudo if enabled
        if ($this->sudo) {
            $this->bin = $this->sudo . ' ' . $this->bin;
        }

        // add parameters to command
        $this->bin .= implode(' ', $this->params);

        if (!is_null($this->pre)) {
            call_user_func($this->pre, $this->bin);
        }

        $this->start = time();

        $this->adapter->run($this->bin, $live);

        $this->end = time();

        if (!is_null($this->post)) {
            call_user_func_array($this->post, [$this->bin, $this->getExitStatus(), $this->start, $this->end, $this->getDuration()]);
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
    public function getBin()
    {
        return $this->bin;
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

    /**
     * Return the parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Return start timestamp
     *
     * @return integer
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Return end timestamp
     *
     * @return integer
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Return the duration of command exec
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->end - $this->start;
    }
}