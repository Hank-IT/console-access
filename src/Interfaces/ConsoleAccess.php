<?php

namespace MrCrankHank\ConsoleAccess\Interfaces;

interface ConsoleAccessInterface {
    public function __construct(AdapterInterface $adapter);

    public function getOutput();

    public function getExitStatus();
}