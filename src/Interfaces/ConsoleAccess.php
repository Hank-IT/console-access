<?php

namespace MrCrankHank\ConsoleAccess\Interfaces;

use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;

interface ConsoleAccessInterface {
    public function __construct(AdapterInterface $adapter);
}