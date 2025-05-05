<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;
use DigitalOceanDropletBundle\Enum\DropletActionType;

/**
 * 根据标签执行Droplet操作请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_post_byTag
 */
class PerformDropletActionsByTagRequest extends DigitalOceanRequest
{
    private string $tagName;
    private array $payload = [
        'type' => '',
    ];

    public function __construct(string $tagName, DropletActionType|string $actionType)
    {
        $this->tagName = $tagName;
        $this->payload['type'] = $actionType instanceof DropletActionType ? $actionType->value : $actionType;
    }

    public function getRequestMethod(): ?string
    {
        return 'POST';
    }

    public function getRequestPath(): string
    {
        return '/droplets/actions?tag_name=' . urlencode($this->tagName);
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
    public static function reboot(string $tagName): self
    {
        return new self($tagName, DropletActionType::REBOOT);
    }

    /**
     * 关闭电源
     */
    public static function powerOff(string $tagName): self
    {
        return new self($tagName, DropletActionType::POWER_OFF);
    }

    /**
     * 开启电源
     */
    public static function powerOn(string $tagName): self
    {
        return new self($tagName, DropletActionType::POWER_ON);
    }

    /**
     * 关机
     */
    public static function shutdown(string $tagName): self
    {
        return new self($tagName, DropletActionType::SHUTDOWN);
    }

    /**
     * 启用IPv6
     */
    public static function enableIpv6(string $tagName): self
    {
        return new self($tagName, DropletActionType::ENABLE_IPV6);
    }

    /**
     * 启用备份
     */
    public static function enableBackups(string $tagName): self
    {
        return new self($tagName, DropletActionType::ENABLE_BACKUPS);
    }

    /**
     * 禁用备份
     */
    public static function disableBackups(string $tagName): self
    {
        return new self($tagName, DropletActionType::DISABLE_BACKUPS);
    }

    /**
     * 快照操作
     */
    public static function snapshot(string $tagName, string $name): self
    {
        $request = new self($tagName, DropletActionType::SNAPSHOT);
        $request->payload['name'] = $name;
        return $request;
    }
}
