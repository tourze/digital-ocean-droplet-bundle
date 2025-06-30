<?php

namespace DigitalOceanDropletBundle\Tests\Unit;

use DigitalOceanDropletBundle\DigitalOceanDropletBundle;
use PHPUnit\Framework\TestCase;

class DigitalOceanDropletBundleTest extends TestCase
{
    public function testGetName(): void
    {
        $bundle = new DigitalOceanDropletBundle();
        $this->assertEquals('DigitalOceanDropletBundle', $bundle->getName());
    }
}