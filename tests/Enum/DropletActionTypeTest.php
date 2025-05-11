<?php

namespace DigitalOceanDropletBundle\Tests\Enum;

use DigitalOceanDropletBundle\Enum\DropletActionType;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DropletActionTypeTest extends TestCase
{
    public function testEnumValues(): void
    {
        $this->assertEquals('reboot', DropletActionType::REBOOT->value);
        $this->assertEquals('power_off', DropletActionType::POWER_OFF->value);
        $this->assertEquals('power_on', DropletActionType::POWER_ON->value);
        $this->assertEquals('shutdown', DropletActionType::SHUTDOWN->value);
        $this->assertEquals('restore', DropletActionType::RESTORE->value);
        $this->assertEquals('password_reset', DropletActionType::PASSWORD_RESET->value);
        $this->assertEquals('resize', DropletActionType::RESIZE->value);
        $this->assertEquals('rebuild', DropletActionType::REBUILD->value);
        $this->assertEquals('rename', DropletActionType::RENAME->value);
        $this->assertEquals('change_kernel', DropletActionType::CHANGE_KERNEL->value);
        $this->assertEquals('enable_ipv6', DropletActionType::ENABLE_IPV6->value);
        $this->assertEquals('enable_backups', DropletActionType::ENABLE_BACKUPS->value);
        $this->assertEquals('disable_backups', DropletActionType::DISABLE_BACKUPS->value);
        $this->assertEquals('enable_private_networking', DropletActionType::ENABLE_PRIVATE_NETWORKING->value);
        $this->assertEquals('snapshot', DropletActionType::SNAPSHOT->value);
    }
    
    public function testGetLabel(): void
    {
        $this->assertEquals('重启', DropletActionType::REBOOT->getLabel());
        $this->assertEquals('关闭电源', DropletActionType::POWER_OFF->getLabel());
        $this->assertEquals('开启电源', DropletActionType::POWER_ON->getLabel());
        $this->assertEquals('关机', DropletActionType::SHUTDOWN->getLabel());
        $this->assertEquals('还原', DropletActionType::RESTORE->getLabel());
        $this->assertEquals('重置密码', DropletActionType::PASSWORD_RESET->getLabel());
        $this->assertEquals('调整大小', DropletActionType::RESIZE->getLabel());
        $this->assertEquals('重建', DropletActionType::REBUILD->getLabel());
        $this->assertEquals('重命名', DropletActionType::RENAME->getLabel());
        $this->assertEquals('更改内核', DropletActionType::CHANGE_KERNEL->getLabel());
        $this->assertEquals('启用IPv6', DropletActionType::ENABLE_IPV6->getLabel());
        $this->assertEquals('启用备份', DropletActionType::ENABLE_BACKUPS->getLabel());
        $this->assertEquals('禁用备份', DropletActionType::DISABLE_BACKUPS->getLabel());
        $this->assertEquals('启用私有网络', DropletActionType::ENABLE_PRIVATE_NETWORKING->getLabel());
        $this->assertEquals('创建快照', DropletActionType::SNAPSHOT->getLabel());
    }
    
    public function testItemTraitMethods(): void
    {
        $reflectionClass = new ReflectionClass(DropletActionType::class);
        $cases = $reflectionClass->getConstants();
        
        // 验证枚举项数量
        $this->assertCount(15, $cases);
        
        foreach ($cases as $case) {
            $this->assertInstanceOf(DropletActionType::class, $case);
        }
    }
    
    public function testSelectTraitMethods(): void
    {
        $allLabels = [
            'reboot' => '重启',
            'power_off' => '关闭电源',
            'power_on' => '开启电源',
            'shutdown' => '关机',
            'restore' => '还原',
            'password_reset' => '重置密码',
            'resize' => '调整大小',
            'rebuild' => '重建',
            'rename' => '重命名',
            'change_kernel' => '更改内核',
            'enable_ipv6' => '启用IPv6',
            'enable_backups' => '启用备份',
            'disable_backups' => '禁用备份',
            'enable_private_networking' => '启用私有网络',
            'snapshot' => '创建快照',
        ];
        
        foreach ($allLabels as $value => $label) {
            $case = DropletActionType::from($value);
            $this->assertEquals($label, $case->getLabel());
        }
    }
    
    public function testFromValue(): void
    {
        $type = DropletActionType::from('reboot');
        $this->assertSame(DropletActionType::REBOOT, $type);
        
        $type = DropletActionType::from('power_off');
        $this->assertSame(DropletActionType::POWER_OFF, $type);
        
        $type = DropletActionType::from('power_on');
        $this->assertSame(DropletActionType::POWER_ON, $type);
    }
    
    public function testTryFrom(): void
    {
        $type = DropletActionType::tryFrom('reboot');
        $this->assertSame(DropletActionType::REBOOT, $type);
        
        $invalidType = DropletActionType::tryFrom('invalid_action');
        $this->assertNull($invalidType);
    }
} 