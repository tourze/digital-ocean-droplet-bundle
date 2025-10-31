<?php

namespace DigitalOceanDropletBundle\Tests\Service;

use DigitalOceanDropletBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 自定义初始化逻辑
    }

    public function testServiceInstantiation(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testMenuProviderInterface(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }
}
