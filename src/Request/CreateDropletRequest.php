<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 创建虚拟机请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplets/operation/droplets_create
 */
class CreateDropletRequest extends DigitalOceanRequest
{
    protected string $method = 'POST';
    protected string $endpoint = 'droplets';
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

    public function setName(string $name): self
    {
        $this->payload['name'] = $name;
        return $this;
    }

    public function setRegion(string $region): self
    {
        $this->payload['region'] = $region;
        return $this;
    }

    public function setSize(string $size): self
    {
        $this->payload['size'] = $size;
        return $this;
    }

    public function setImage(string $image): self
    {
        $this->payload['image'] = $image;
        return $this;
    }

    public function setSshKeys(array $sshKeys): self
    {
        $this->payload['ssh_keys'] = $sshKeys;
        return $this;
    }

    public function setBackups(bool $backups): self
    {
        $this->payload['backups'] = $backups;
        return $this;
    }

    public function setIpv6(bool $ipv6): self
    {
        $this->payload['ipv6'] = $ipv6;
        return $this;
    }

    public function setMonitoring(bool $monitoring): self
    {
        $this->payload['monitoring'] = $monitoring;
        return $this;
    }

    public function setTags(array $tags): self
    {
        $this->payload['tags'] = $tags;
        return $this;
    }

    public function addTag(string $tag): self
    {
        if (!in_array($tag, $this->payload['tags'])) {
            $this->payload['tags'][] = $tag;
        }
        return $this;
    }
}
