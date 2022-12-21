<?php

namespace Tests\Adapters\SshAdapter;

use Hamcrest\Core\IsEqual;
use HankIT\ConsoleAccess\Adapters\SshAdapter\Adapter;
use HankIT\ConsoleAccess\Adapters\SshAdapter\Password;
use Mockery;
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

        $adapter = new Adapter($sshMock, $this->fake()->userName, new Password('test'));

        $this->assertTrue($adapter->available());
    }

    /**
     * @test
     */
    public function it_gets_the_server_public_key()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $sshMock->shouldReceive('getServerPublicHostKey')->once()->andReturn($content = $this->fake()->randomHtml);

        $adapter = new Adapter($sshMock, $this->fake()->userName, new Password('test'));

        $this->assertEquals($content, $adapter->getServerPublicHostKey());
    }

    /**
     * @test
     */
    public function it_authenticates_using_a_password_runs_a_command_and_verifies_the_output()
    {
        $sshMock = Mockery::mock(SSH2::class);

        $username = $this->fake()->userName;
        $password = $this->fake()->password;
        $command = $this->fake()->randomHtml;
        $output = $this->fake()->randomHtml;

        $sshMock->shouldReceive('login')->withArgs([
            IsEqual::equalTo($username),
            IsEqual::equalTo($password),
        ])->once()->andReturnTrue();

        $sshMock->shouldReceive('exec')->withArgs([
            IsEqual::equalTo($command),
            IsEqual::equalTo(null),
        ])->once()->andReturn($output);

        $sshMock->shouldReceive('ping')->once()->andReturnTrue();

        $adapter = new Adapter($sshMock, $username, new Password($password));

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

        $adapter = new Adapter($sshMock, $this->fake()->userName, new Password('test'));

        $this->assertEquals(0, $adapter->getExitStatus());
    }
}