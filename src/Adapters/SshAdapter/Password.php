<?php

namespace HankIT\ConsoleAccess\Adapters\SshAdapter;

use HankIT\ConsoleAccess\Adapters\Contract\Credential;

class Password implements Credential
{
    protected string $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function get(): string
    {
        return $this->password;
    }
}