<?php

namespace DigitalOceanDropletBundle;

use DigitalOceanAccountBundle\DigitalOceanAccountBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;

class DigitalOceanDropletBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DigitalOceanAccountBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
        ];
    }
}
