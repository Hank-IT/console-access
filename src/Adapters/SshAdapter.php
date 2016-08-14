<?php

namespace MrCrankHank\ConsoleAccess\Adapters;

use Collective\Remote\Connection;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;

class SshAdapter implements AdapterInterface {
    private $connection;

    public function __construct($host, $username, array $auth)
    {
        $this->connection = new Connection('connection', $host, $username, $auth);
    }

    public function run($command, Closure $live = null)
    {
        $this->connection->run($command, $live);
    }
}