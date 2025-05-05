<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取Droplet操作列表请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplet-Actions/operation/dropletActions_list
 */
class ListDropletActionsRequest extends DigitalOceanRequest
{
    private int $dropletId;
    private int $page = 1;
    private int $perPage = 20;

    public function __construct(int $dropletId)
    {
        $this->dropletId = $dropletId;
    }

    public function setPage(int $page): self
    {
        $this->page = max(1, $page);
        return $this;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = min(200, max(1, $perPage));
        return $this;
    }

    public function getRequestPath(): string
    {
        return '/droplets/' . $this->dropletId . '/actions?' . http_build_query([
            'page' => $this->page,
            'per_page' => $this->perPage,
        ]);
    }
}
