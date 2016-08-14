<?php

namespace MrCrankHank\ConsoleAccess;

use MrCrankHank\ConsoleAccess\Interfaces\ConsoleAccessInterface;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class ConsoleAccess implements ConsoleAccessInterface {
    private $adapter;

    private $sudo;

    public function __construct(AdapterInterface $adapter, $sudo = '/usr/bin/sudo')
    {
        $this->adapter = $adapter;

        $this->sudo = $sudo;
    }

    public function exec($command, Closure $live = null)
    {
        if ($this->sudo === false) {
            $this->adapter->run($command, $live);
        } else {
            $this->adapter->run($this->sudo . ' ' . $command, $live);
        }
    }
}