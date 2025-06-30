<?php

namespace DigitalOceanDropletBundle\Tests\Unit\Exception;

use DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException;
use PHPUnit\Framework\TestCase;

class DigitalOceanConfigurationExceptionTest extends TestCase
{
    public function testExceptionIsThrowable(): void
    {
        $exception = new DigitalOceanConfigurationException('Test message');
        
        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}