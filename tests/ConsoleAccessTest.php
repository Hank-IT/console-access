<?php

namespace Tests;

use Hamcrest\Core\IsEqual;
use HankIT\ConsoleAccess\Adapters\DummyAdapter;
use HankIT\ConsoleAccess\ConsoleAccess;
use Mockery;

class ConsoleAccessTest extends TestCase
{
    /**
     * @test
     */
    public function it_executes_a_command()
    {
        $adapter = Mockery::mock(DummyAdapter::class);

        $adapter->shouldReceive('run')->once()->withArgs([
            IsEqual::equalTo('/usr/bin/sudo getenv "group" "users"'),
            IsEqual::equalTo(null),
        ]);

        $console = new ConsoleAccess($adapter);

        $console->sudo()->bin('getenv')->param('group')->param('users')->exec();
    }
}