<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取Droplet操作详情请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_get
 */
class GetDropletActionRequest extends DigitalOceanRequest
{
    private int $dropletId;
    private int $actionId;

    public function __construct(int $dropletId, int $actionId)
    {
        $this->dropletId = $dropletId;
        $this->actionId = $actionId;
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId . '/actions/' . $this->actionId;
    }
}
