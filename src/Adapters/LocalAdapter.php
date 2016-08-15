<?php

namespace MrCrankHank\ConsoleAccess\Adapters;

use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class LocalAdapter implements AdapterInterface {
    private $exitStatus;

    private $output;

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

        $this->output = fread($run, 4096);

        $this->exitStatus = pclose($run);
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function getExitStatus()
    {
        return $this->exitStatus;
    }
}