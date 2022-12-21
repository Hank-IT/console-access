<?php

namespace HankIT\ConsoleAccess\Adapters\SshAdapter;

use phpseclib3\Crypt\Common\AsymmetricKey;
use phpseclib3\Crypt\PublicKeyLoader;

class Key
{
    protected AsymmetricKey $key;

    public function __construct(string $key, ?string $password = null)
    {
        $this->key = PublicKeyLoader::load($key, is_null($password) ? false : $password);
    }

    public function get(): AsymmetricKey
    {
        return $this->key;
    }
}