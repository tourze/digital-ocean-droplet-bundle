<?php

namespace DigitalOceanDropletBundle\Request;

use DigitalOceanAccountBundle\Request\DigitalOceanRequest;

/**
 * 获取SSH密钥列表请求
 *
 * @see https://docs.digitalocean.com/reference/api/digitalocean/#tag/SSH-Keys/operation/sshKeys_list
 */
class ListSSHKeysRequest extends DigitalOceanRequest
{
    protected string $method = 'GET';

    private int $page = 1;

    private int $perPage = 20;

    public function setPage(int $page): void
    {
        $this->page = max(1, $page);
    }

    public function setPerPage(int $perPage): void
    {
        $this->perPage = min(200, max(1, $perPage));
    }

    public function getRequestPath(): string
    {
        return '/account/keys?' . http_build_query([
            'page' => $this->page,
            'per_page' => $this->perPage,
        ]);
    }
}
