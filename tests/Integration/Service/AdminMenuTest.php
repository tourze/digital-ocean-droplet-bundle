<?php

namespace DigitalOceanDropletBundle\Tests\Integration\Service;

use DigitalOceanDropletBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    public function testInvoke(): void
    {
        $linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $linkGenerator->method('getCurdListPage')->willReturn('/admin/droplet');
        
        $digitalOceanMenu = $this->createMock(ItemInterface::class);
        $digitalOceanMenu->method('addChild')->willReturnSelf();
        $digitalOceanMenu->method('setUri')->willReturnSelf();
        $digitalOceanMenu->method('setAttribute')->willReturnSelf();
        
        $cloudMenu = $this->createMock(ItemInterface::class);
        $cloudMenu->method('getChild')->willReturn($digitalOceanMenu);
        $cloudMenu->method('addChild')->willReturn($digitalOceanMenu);
        
        $menuItem = $this->createMock(ItemInterface::class);
        $menuItem->method('getChild')->willReturn($cloudMenu);
        $menuItem->method('addChild')->willReturn($cloudMenu);
        
        $adminMenu = new AdminMenu($linkGenerator);
        
        $adminMenu($menuItem);
        
        $this->assertTrue(true);
    }
}