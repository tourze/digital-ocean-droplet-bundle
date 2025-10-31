<?php

declare(strict_types=1);

namespace DigitalOceanDropletBundle\Tests;

use DigitalOceanDropletBundle\DigitalOceanDropletBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DigitalOceanDropletBundle::class)]
#[RunTestsInSeparateProcesses]
final class DigitalOceanDropletBundleTest extends AbstractBundleTestCase
{
}
