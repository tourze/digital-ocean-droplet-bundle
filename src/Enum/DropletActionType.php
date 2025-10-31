<?php

namespace DigitalOceanDropletBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * Droplet 操作类型枚举
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_post
 */
enum DropletActionType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;
    case REBOOT = 'reboot';
    case POWER_OFF = 'power_off';
    case POWER_ON = 'power_on';
    case SHUTDOWN = 'shutdown';
    case RESTORE = 'restore';
    case PASSWORD_RESET = 'password_reset';
    case RESIZE = 'resize';
    case REBUILD = 'rebuild';
    case RENAME = 'rename';
    case CHANGE_KERNEL = 'change_kernel';
    case ENABLE_IPV6 = 'enable_ipv6';
    case ENABLE_BACKUPS = 'enable_backups';
    case DISABLE_BACKUPS = 'disable_backups';
    case ENABLE_PRIVATE_NETWORKING = 'enable_private_networking';
    case SNAPSHOT = 'snapshot';

    public function getLabel(): string
    {
        return match ($this) {
            self::REBOOT => '重启',
            self::POWER_OFF => '关闭电源',
            self::POWER_ON => '开启电源',
            self::SHUTDOWN => '关机',
            self::RESTORE => '还原',
            self::PASSWORD_RESET => '重置密码',
            self::RESIZE => '调整大小',
            self::REBUILD => '重建',
            self::RENAME => '重命名',
            self::CHANGE_KERNEL => '更改内核',
            self::ENABLE_IPV6 => '启用IPv6',
            self::ENABLE_BACKUPS => '启用备份',
            self::DISABLE_BACKUPS => '禁用备份',
            self::ENABLE_PRIVATE_NETWORKING => '启用私有网络',
            self::SNAPSHOT => '创建快照',
        };
    }
}
