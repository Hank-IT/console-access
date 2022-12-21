<?php

namespace HankIT\ConsoleAccess\Adapters;

use HankIT\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class LocalAdapter implements AdapterInterface
{
    protected string $exitStatus;

    protected string $output = '';

    /**
     * Run a command.
     *
     * @param string       $command Command which should be run
     * @param Closure|null $live    Closure to capture the live output of the command
     */
    public function run($command, Closure $live = null)
    {
        while (@ob_end_flush());

        $run = popen($command . ' 2>&1', 'r');

        while (! feof($run)) {
            $line = fread($run, 4096);

            $this->output .= $line;

            if (! is_null($live)) {
                call_user_func($live, $line);
            }

            @flush();
        }

        $this->exitStatus = pclose($run);
    }

    /**
     * Return the output of the last command.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return the exist status of the last command.
     *
     * @return mixed
     */
    public function getExitStatus()
    {
        return $this->exitStatus;
    }
}
