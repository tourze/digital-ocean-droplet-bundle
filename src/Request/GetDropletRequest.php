<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取单个虚拟机请求
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplets/operation/droplets_get
 */
class GetDropletRequest extends DigitalOceanRequest
{
    public function __construct(private readonly int $dropletId)
    {
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId;
    }
}
