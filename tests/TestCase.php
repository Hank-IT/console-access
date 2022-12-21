<?php

namespace Tests;

use Faker\Factory;
use Faker\Generator;
use Mockery;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

    public function fake(): Generator
    {
        return Factory::create();
    }
}