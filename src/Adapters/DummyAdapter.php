<?php

namespace HankIT\ConsoleAccess\Adapters;

use HankIT\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class DummyAdapter implements AdapterInterface
{
    public function run(string $command, Closure $live = null): void {}

    public function getOutput(): string
    {
        return "dummy";
    }

    public function getExitStatus(): int
    {
        return 0;
    }
}
