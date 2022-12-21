<?php

namespace Tests\Adapters\SshAdapter;

use HankIT\ConsoleAccess\Adapters\SshAdapter\Key;
use phpseclib3\Crypt\EC;
use Tests\TestCase;

class KeyTest extends TestCase
{
    public function key_data_provider(): array
    {
        return [
            [$this->getClearTextEcPrivateKey(), null],
            [$this->getEncryptedEcPrivateKey(), 'password'],
        ];
    }

    protected function getClearTextEcPrivateKey(): string
    {
        return "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtz\nc2gtZWQyNTUxOQAAACBILfCZGUhmIGgm+5zmcCvvDE4iYwnGx1zsVpOQ24NAjQAA\nAKCE2ZLxhNmS8QAAAAtzc2gtZWQyNTUxOQAAACBILfCZGUhmIGgm+5zmcCvvDE4i\nYwnGx1zsVpOQ24NAjQAAAEC7O4xjSTxQxZcpftnuwdei3EOXxSa1taCHQr5jtUtQ\n1Ugt8JkZSGYgaCb7nOZwK+8MTiJjCcbHXOxWk5Dbg0CNAAAAFGVkMjU1MTkta2V5\nLTIwMjIxMjIxAQIDBAUGBwgJ\n-----END OPENSSH PRIVATE KEY-----";
    }

    protected function getEncryptedEcPrivateKey(): string
    {
        return "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAACmFlczI1Ni1jdHIAAAAGYmNyeXB0AAAAGAAAABAo\nbeiVgSyOEGNDkxk4CFAKAAAAEAAAAAEAAAAzAAAAC3NzaC1lZDI1NTE5AAAAIEgt\n8JkZSGYgaCb7nOZwK+8MTiJjCcbHXOxWk5Dbg0CNAAAAoEWLYWXddz9OkYoc45Vg\nY8ofuIg3s6I84hVtyciCi1SFk9SwAYhcTiejMHPD2j0uSDnlAcYbNHfVyPprYcfr\nNbeCBCuBusDKa7a64w4VCdyY1B4pG0fYy+XTHpVcE/qeIt7GF0JQaGBGr7Patnmr\nMDrqyxGkm5VDdhlkLF9M6GTZOU+PVkltQKRP9VwJdDH12Z5YIMVnHqYwbzD2FnBl\nCQ0=\n-----END OPENSSH PRIVATE KEY-----";
    }

    /**
     * @dataProvider key_data_provider
     */
    public function test(string $key, ?string $password)
    {
        $key = new Key($key, $password);

        $this->assertInstanceOf(EC::class, $key->get());
    }
}