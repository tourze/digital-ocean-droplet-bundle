<?php

namespace DigitalOceanDropletBundle\Tests\Unit\DependencyInjection;

use DigitalOceanDropletBundle\DependencyInjection\DigitalOceanDropletExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class DigitalOceanDropletExtensionTest extends TestCase
{
    public function testLoad(): void
    {
        $extension = new DigitalOceanDropletExtension();
        $container = new ContainerBuilder();
        
        $extension->load([], $container);
        
        $this->assertNotEmpty($container->getDefinitions());
    }
}