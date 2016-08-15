<?php

namespace MrCrankHank\ConsoleAccess\Adapters;

use Mockery\CountValidator\Exception;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

class SshAdapter implements AdapterInterface {
    private $connection;

    private $username;

    private $publicKey;

    public function __construct($host, $username, $publicKey)
    {
        $this->connection = new SSH2($host);

        if ($this->connection->getServerPublicHostKey() !== $publicKey) {
            throw new Exception('Public key mismatch');
        }

        $this->username = $username;

        $this->publicKey = $publicKey;
    }

    public function loginPassword($password)
    {
        $this->_login($password);
    }

    public function loginKey($key, $password = null)
    {
        $crypt = new RSA;

        if (!is_null($password)) {
            $crypt->setPassword($password);
        }

        if (file_exists($key)) {
            $crypt->loadKey(file_get_contents($key));
        } else {
            $crypt->loadKey($key);
        }

        $this->_login($crypt);
    }

    public function run($command, Closure $live = null)
    {
        $this->connection->exec($command, $live);
    }

    private function _login($auth)
    {
        if (!$this->connection->login($this->username, $auth)) {
            throw new Exception("Not connected");
        }
    }
}