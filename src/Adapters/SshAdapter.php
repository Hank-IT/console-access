<?php

/**
 * This file contains the SshAdapter class.
 * It implements the access to the console of
 * a remote server.
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Adapters
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */

namespace MrCrankHank\ConsoleAccess\Adapters;

use MrCrankHank\ConsoleAccess\Exceptions\ConnectionNotPossibleException;
use MrCrankHank\ConsoleAccess\Exceptions\PublicKeyMismatchException;
use MrCrankHank\ConsoleAccess\Interfaces\AdapterInterface;
use Closure;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;

/**
 * Class SshAdapter
 *
 * PHP version 5.6
 *
 * @category Console
 * @package  MrCrankHank\ConsoleAccess\Adapters
 * @author   Alexander Hank <mail@alexander-hank.de>
 * @license  Apache License 2.0 http://www.apache.org/licenses/LICENSE-2.0.txt
 * @link     null
 */
class SshAdapter implements AdapterInterface {
    /**
     * @var SSH2
     */
    private $connection;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $output;

    /**
     * SshAdapter constructor.
     *
     * @throws PublicKeyMismatchException
     *
     * @param $host
     * @param $username
     * @param $publicKey
     */
    public function __construct($host, $username, $publicKey)
    {
        $this->connection = new SSH2($host);

        if ($this->connection->getServerPublicHostKey() !== $publicKey) {
            throw new PublicKeyMismatchException('Public key mismatch');
        }

        $this->username = $username;

        $this->publicKey = $publicKey;
    }

    /**
     * Login via password
     *
     * @param $password
     */
    public function loginPassword($password)
    {
        $this->_login($password);
    }

    /**
     * Login via private key
     *
     * @param      $key
     * @param null $password
     */
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

    /**
     * Run a command on the remote server.
     *
     * @param  string      $command Command which should be run
     * @param Closure|null $live    Closure to capture the live output of the command
     */
    public function run($command, Closure $live = null)
    {
        $this->output = $this->connection->exec($command, $live);
    }

    /**
     * Return the output of the last command.
     * Does not work well with long running commands.
     * You should capture the live output instead.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Return the exist status of the last command
     *
     * @return mixed
     */
    public function getExitStatus()
    {
        return $this->connection->getExitStatus();
    }

    /**
     * Login to the server.
     *
     * @throws ConnectionNotPossibleException
     *
     * @param $auth
     */
    private function _login($auth)
    {
        if (!$this->connection->login($this->username, $auth)) {
            throw new ConnectionNotPossibleException("Not connected");
        }
    }
}