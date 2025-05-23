<?php

namespace DigitalOceanDropletBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class DigitalOceanDropletBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            \DigitalOceanAccountBundle\DigitalOceanAccountBundle::class => ['all' => true],
        ];
    }
}
