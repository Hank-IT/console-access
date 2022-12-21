<?php

namespace HankIT\ConsoleAccess\Adapters\SshAdapter;

use Closure;
use HankIT\ConsoleAccess\Exceptions\ConnectionNotPossibleException;
use HankIT\ConsoleAccess\Exceptions\PublicKeyMismatchException;
use HankIT\ConsoleAccess\Interfaces\AdapterInterface;
use phpseclib3\Net\SSH2;

class Adapter implements AdapterInterface
{
    protected SSH2 $connection;

    protected string $username;

    protected ?string $publicKey;

    protected ?string $output = null;

    public function __construct(
        SSH2 $connection,
        string $username,
        ?string $publicKey = null
    )
    {
        $this->connection = $connection;

        $this->username = $username;

        $this->publicKey = $publicKey;
    }

    public function available(): bool
    {
        return $this->connection->isConnected();
    }

    public function getServerPublicHostKey(): string
    {
        return $this->connection->getServerPublicHostKey();
    }

    public function loginPassword(string $password): void
    {
        if (! is_null($this->publicKey) && $this->getServerPublicHostKey() !== $this->publicKey) {
            throw new PublicKeyMismatchException('Public key mismatch');
        }

        $this->login($password);
    }

    public function loginKey(Key $key): void
    {
        if (! is_null($this->publicKey) && $this->getServerPublicHostKey() !== $this->publicKey) {
            throw new PublicKeyMismatchException('Public key mismatch');
        }

        $this->login($key->get());
    }

    public function run(string $command, Closure $live = null): void
    {
        $this->output = $this->connection->exec($command, $live);
    }

    public function getOutput(): ?string
    {
        return $this->output;
    }

    public function getExitStatus(): int
    {
        return $this->connection->getExitStatus();
    }
    
    protected function login($auth): void
    {
        if (! $this->connection->login($this->username, $auth)) {
            throw new ConnectionNotPossibleException('Not connected');
        }
    }
}
