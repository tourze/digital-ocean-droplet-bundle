<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanDropletBundle\Entity\Droplet;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * DigitalOcean虚拟机管理菜单服务
 */
#[Autoconfigure(public: true)]
#[AutoconfigureTag(name: 'tourze.easy_admin_menu.provider')]
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('云服务管理')) {
            $item->addChild('云服务管理');
        }

        $cloudMenu = $item->getChild('云服务管理');

        if (null === $cloudMenu) {
            return;
        }

        // DigitalOcean 子菜单
        if (null === $cloudMenu->getChild('DigitalOcean')) {
            $cloudMenu->addChild('DigitalOcean');
        }

        $digitalOceanMenu = $cloudMenu->getChild('DigitalOcean');

        if (null === $digitalOceanMenu) {
            return;
        }

        // 虚拟机管理菜单
        $digitalOceanMenu->addChild('虚拟机管理')
            ->setUri($this->linkGenerator->getCurdListPage(Droplet::class))
            ->setAttribute('icon', 'fas fa-server')
        ;
    }
}
