<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;
use DigitalOceanDropletBundle\Enum\DropletActionType;

/**
 * 执行Droplet操作请求
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_post
 */
class PerformDropletActionRequest extends DigitalOceanRequest
{
    /** @var array<string, mixed> */
    private array $payload = [
        'type' => '',
    ];

    public function __construct(private readonly int $dropletId, DropletActionType|string $actionType)
    {
        $this->payload['type'] = $actionType instanceof DropletActionType ? $actionType->value : $actionType;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId . '/actions';
    }

    public function getRequestOptions(): ?array
    {
        return [
            'json' => $this->payload,
        ];
    }

    /**
     * 重启Droplet
     */
    public static function reboot(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::REBOOT);
    }

    /**
     * 关闭电源
     */
    public static function powerOff(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::POWER_OFF);
    }

    /**
     * 开启电源
     */
    public static function powerOn(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::POWER_ON);
    }

    /**
     * 关机
     */
    public static function shutdown(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::SHUTDOWN);
    }

    /**
     * 启用IPv6
     */
    public static function enableIpv6(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::ENABLE_IPV6);
    }

    /**
     * 启用备份
     */
    public static function enableBackups(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::ENABLE_BACKUPS);
    }

    /**
     * 禁用备份
     */
    public static function disableBackups(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::DISABLE_BACKUPS);
    }

    /**
     * 重置密码
     */
    public static function passwordReset(int $dropletId): self
    {
        return new self($dropletId, DropletActionType::PASSWORD_RESET);
    }

    /**
     * 重建Droplet
     */
    public function rebuild(int $imageId): self
    {
        $this->payload['type'] = DropletActionType::REBUILD->value;
        $this->payload['image'] = $imageId;

        return $this;
    }

    /**
     * 调整Droplet规格
     */
    public function resize(string $size, bool $disk = true): self
    {
        $this->payload['type'] = DropletActionType::RESIZE->value;
        $this->payload['size'] = $size;
        $this->payload['disk'] = $disk;

        return $this;
    }

    /**
     * 重命名Droplet
     */
    public function rename(string $name): self
    {
        $this->payload['type'] = DropletActionType::RENAME->value;
        $this->payload['name'] = $name;

        return $this;
    }

    /**
     * 快照Droplet
     */
    public static function snapshot(int $dropletId, string $name): self
    {
        $request = new self($dropletId, DropletActionType::SNAPSHOT);
        $request->payload['name'] = $name;

        return $request;
    }

    /**
     * 使用快照恢复Droplet
     */
    public static function restore(int $dropletId, int $imageId): self
    {
        $request = new self($dropletId, DropletActionType::RESTORE);
        $request->payload['image'] = $imageId;

        return $request;
    }
}
