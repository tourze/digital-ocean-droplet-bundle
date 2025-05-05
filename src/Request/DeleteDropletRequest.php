<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 删除虚拟机请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplets/operation/droplets_delete
 */
class DeleteDropletRequest extends DigitalOceanRequest
{
    protected string $method = 'DELETE';
    private int $dropletId;

    public function __construct(int $dropletId)
    {
        $this->dropletId = $dropletId;
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId;
    }
}
