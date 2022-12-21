<?php

namespace HankIT\ConsoleAccess\Adapters\SshAdapter;

use Closure;
use HankIT\ConsoleAccess\Adapters\Contract\Credential;
use HankIT\ConsoleAccess\Exceptions\ConnectionNotPossibleException;
use HankIT\ConsoleAccess\Exceptions\PublicKeyMismatchException;
use HankIT\ConsoleAccess\Interfaces\AdapterInterface;
use phpseclib3\Net\SSH2;

class Adapter implements AdapterInterface
{
    protected SSH2 $connection;

    protected string $username;

    protected Credential  $credential;

    protected ?string $publicKey;

    protected ?string $output = null;

    protected bool $initialLogin = false;

    public function __construct(
        SSH2 $connection,
        string $username,
        Credential $credential,
        ?string $publicKey = null
    ){
        $this->connection = $connection;

        $this->username = $username;

        $this->credential = $credential;

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

    public function run(string $command, Closure $live = null): void
    {
        if (! $this->initialLogin) {
            $this->login();

            $this->initialLogin = true;
        }

        if (! $this->connection->ping()) {
            throw new ConnectionNotPossibleException('Ping failed');
        }

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

    protected function login(): void
    {
        if (! is_null($this->publicKey) && $this->getServerPublicHostKey() !== $this->publicKey) {
            throw new PublicKeyMismatchException('Public key mismatch');
        }

        if (! $this->connection->login($this->username, $this->credential->get())) {
            throw new ConnectionNotPossibleException('Not connected');
        }
    }
}
