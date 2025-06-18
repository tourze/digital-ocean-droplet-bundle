<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanDropletBundle\Entity\Droplet;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * DigitalOcean虚拟机管理菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if ($item->getChild('云服务管理') === null) {
            $item->addChild('云服务管理');
        }

        $cloudMenu = $item->getChild('云服务管理');
        
        // DigitalOcean 子菜单
        if ($cloudMenu->getChild('DigitalOcean') === null) {
            $cloudMenu->addChild('DigitalOcean');
        }
        
        $digitalOceanMenu = $cloudMenu->getChild('DigitalOcean');
        
        // 虚拟机管理菜单
        $digitalOceanMenu->addChild('虚拟机管理')
            ->setUri($this->linkGenerator->getCurdListPage(Droplet::class))
            ->setAttribute('icon', 'fas fa-server');
    }
} 