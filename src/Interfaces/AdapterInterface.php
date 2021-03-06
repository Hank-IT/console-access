<?php

namespace MrCrankHank\ConsoleAccess\Interfaces;

use Closure;

interface AdapterInterface
{
    public function run($command, Closure $live = null);

    public function getOutput();

    public function getExitStatus();
}
