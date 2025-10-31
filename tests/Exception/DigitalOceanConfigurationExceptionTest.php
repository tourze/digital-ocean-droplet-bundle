<?php

namespace DigitalOceanDropletBundle\Tests\Exception;

use DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitBase\AbstractExceptionTestCase;

/**
 * @internal
 */
#[CoversClass(DigitalOceanConfigurationException::class)]
final class DigitalOceanConfigurationExceptionTest extends AbstractExceptionTestCase
{
    public function testExceptionIsThrowable(): void
    {
        $exception = new DigitalOceanConfigurationException('Test message');

        $this->assertInstanceOf(\RuntimeException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
    }
}
