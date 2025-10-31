<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 创建虚拟机请求
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplets/operation/droplets_create
 */
class CreateDropletRequest extends DigitalOceanRequest
{
    protected string $method = 'POST';

    protected string $endpoint = 'droplets';

    /** @var array<string, mixed> */
    protected array $payload = [
        'name' => '',
        'region' => 'sgp1', // 默认新加坡
        'size' => 's-1vcpu-1gb', // 默认最小实例
        'image' => 'centos-7-x64',
        'ssh_keys' => [],
        'backups' => false,
        'ipv6' => false,
        'monitoring' => true,
        'tags' => [],
    ];

    public function getRequestPath(): string
    {
        return '/droplets';
    }

    public function setName(string $name): void
    {
        $this->payload['name'] = $name;
    }

    public function setRegion(string $region): void
    {
        $this->payload['region'] = $region;
    }

    public function setSize(string $size): void
    {
        $this->payload['size'] = $size;
    }

    public function setImage(string $image): void
    {
        $this->payload['image'] = $image;
    }

    /**
     * @param array<string> $sshKeys
     */
    public function setSshKeys(array $sshKeys): void
    {
        $this->payload['ssh_keys'] = $sshKeys;
    }

    public function setBackups(bool $backups): void
    {
        $this->payload['backups'] = $backups;
    }

    public function setIpv6(bool $ipv6): void
    {
        $this->payload['ipv6'] = $ipv6;
    }

    public function setMonitoring(bool $monitoring): void
    {
        $this->payload['monitoring'] = $monitoring;
    }

    /**
     * @param array<string> $tags
     */
    public function setTags(array $tags): void
    {
        $this->payload['tags'] = $tags;
    }

    public function addTag(string $tag): void
    {
        $tags = $this->payload['tags'];
        assert(is_array($tags), 'Tags must be an array');

        if (!in_array($tag, $tags, true)) {
            $tags[] = $tag;
            $this->payload['tags'] = $tags;
        }
    }
}
