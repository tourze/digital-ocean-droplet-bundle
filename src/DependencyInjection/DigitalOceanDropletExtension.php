<?php

namespace DigitalOceanDropletBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class DigitalOceanDropletExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
