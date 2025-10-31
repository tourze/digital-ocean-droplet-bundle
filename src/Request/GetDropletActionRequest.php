<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取Droplet操作详情请求
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_get
 */
class GetDropletActionRequest extends DigitalOceanRequest
{
    public function __construct(private readonly int $dropletId, private readonly int $actionId)
    {
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId . '/actions/' . $this->actionId;
    }
}
