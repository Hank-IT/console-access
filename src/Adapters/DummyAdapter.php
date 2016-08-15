<?php

namespace MrCrankHank\ConsoleAccess\Adapters;

use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class DummyAdapter implements AdapterInterface {
    public function run($command, Closure $live = null)
    {

    }
}