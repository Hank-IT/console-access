<?php

namespace Tests\Adapters\SshAdapter;

use Hamcrest\Core\IsEqual;
use HankIT\ConsoleAccess\Adapters\SshAdapter\Adapter;
use HankIT\ConsoleAccess\Adapters\SshAdapter\Key;
use Mockery;
use phpseclib3\Crypt\EC;
use phpseclib3\Net\SSH2;
use Tests\TestCase;

class AdapterTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_connection_status()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $sshMock->shouldReceive('isConnected')->once()->andReturn(true);

        $adapter = new Adapter($sshMock, $this->fake()->userName);

        $this->assertTrue($adapter->available());
    }

    /**
     * @test
     */
    public function it_gets_the_server_public_key()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $sshMock->shouldReceive('getServerPublicHostKey')->once()->andReturn($content = $this->fake()->randomHtml);

        $adapter = new Adapter($sshMock, $this->fake()->userName);

        $this->assertEquals($content, $adapter->getServerPublicHostKey());
    }

    /**
     * @test
     */
    public function it_tests_the_password_login_without_public_key_verification()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $username = $this->fake()->userName;
        $password = $this->fake()->password;

        $sshMock->shouldReceive('login')->withArgs([
            IsEqual::equalTo($username),
            IsEqual::equalTo($password),
        ])->once()->andReturnTrue();

        $adapter = new Adapter($sshMock, $username);

        $adapter->loginPassword($password);
    }

    /**
     * @test
     */
    public function it_tests_the_key_login_without_public_key_verification()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $username = $this->fake()->userName;

        $key = "-----BEGIN OPENSSH PRIVATE KEY-----\nb3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAAAMwAAAAtz\nc2gtZWQyNTUxOQAAACBILfCZGUhmIGgm+5zmcCvvDE4iYwnGx1zsVpOQ24NAjQAA\nAKCE2ZLxhNmS8QAAAAtzc2gtZWQyNTUxOQAAACBILfCZGUhmIGgm+5zmcCvvDE4i\nYwnGx1zsVpOQ24NAjQAAAEC7O4xjSTxQxZcpftnuwdei3EOXxSa1taCHQr5jtUtQ\n1Ugt8JkZSGYgaCb7nOZwK+8MTiJjCcbHXOxWk5Dbg0CNAAAAFGVkMjU1MTkta2V5\nLTIwMjIxMjIxAQIDBAUGBwgJ\n-----END OPENSSH PRIVATE KEY-----";

        $sshMock->shouldReceive('login')->withArgs([
            IsEqual::equalTo($username),
            Mockery::on(function($arg) {
                $this->assertInstanceOf(EC::class, $arg);

                return true;
            })
        ])->once()->andReturnTrue();

        $adapter = new Adapter($sshMock, $username);

        $adapter->loginKey(new Key($key));
    }

    /**
     * @test
     */
    public function it_runs_a_command_and_verifies_the_output()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $username = $this->fake()->userName;
        $command = $this->fake()->randomHtml;
        $output = $this->fake()->randomHtml;

        $sshMock->shouldReceive('exec')->withArgs([
            IsEqual::equalTo($command),
            IsEqual::equalTo(null),
        ])->once()->andReturn($output);

        $adapter = new Adapter($sshMock, $username);

        $adapter->run($command);

        $this->assertEquals($output, $adapter->getOutput());
    }

    /**
     * @test
     */
    public function it_gets_the_exist_status()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $sshMock->shouldReceive('getExitStatus')->once()->andReturn(0);

        $adapter = new Adapter($sshMock, $this->fake()->userName);

        $this->assertEquals(0, $adapter->getExitStatus());
    }
}