<?php

namespace HankIT\ConsoleAccess\Interfaces;

use Closure;

interface AdapterInterface
{
    public function run(string $command, Closure $live = null): void;

    public function getOutput(): ?string;

    public function getExitStatus(): int;
}
