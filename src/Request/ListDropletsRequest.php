<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取虚拟机列表请求
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/Droplets/operation/droplets_list
 */
class ListDropletsRequest extends DigitalOceanRequest
{
    private int $page = 1;
    private int $perPage = 20;
    private ?string $tagName = null;

    public function getRequestPath(): string
    {
        return '/droplets';
    }

    public function getRequestOptions(): ?array
    {
        $query = [
            'page' => $this->page,
            'per_page' => $this->perPage,
        ];

        if ($this->tagName !== null) {
            $query['tag_name'] = $this->tagName;
        }

        return [
            'query' => $query,
        ];
    }

    public function setPage(int $page): self
    {
        $this->page = $page;
        return $this;
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function setTagName(?string $tagName): self
    {
        $this->tagName = $tagName;
        return $this;
    }
}
