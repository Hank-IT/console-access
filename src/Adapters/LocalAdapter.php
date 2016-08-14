<?php

namespace MrCrankHank\ConsoleAccess\Adapters;

use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class LocalAdapter implements AdapterInterface {
    public function run($command, Closure $live = null) {

    }

    /**
     * Execute the given command by displaying console output live to the user.
     *
     * @link http://stackoverflow.com/questions/20107147/php-reading-shell-exec-live-output
     *
     *  @param  string  cmd          :  command to be executed
     *  @return array   exit_status  :  exit status of the executed command
     *                  output       :  console output of the executed command
     */
    public function liveExecuteCommand($cmd)
    {

        while (@ ob_end_flush()); // end all output buffers if any

        $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

        $live_output     = "";
        $complete_output = "";

        while (!feof($proc))
        {
            $live_output     = fread($proc, 4096);
            $complete_output = $complete_output . $live_output;
            echo "$live_output";
            @ flush();
        }

        pclose($proc);

        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        return array (
            'exit_status'  => $matches[0],
            'output'       => str_replace("Exit status : " . $matches[0], '', $complete_output)
        );
    }
}