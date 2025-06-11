<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Request\ListSSHKeysRequest;
use Psr\Log\LoggerInterface;

class SSHKeyService
{
    public function __construct(
        private readonly DigitalOceanClient $client,
        private readonly DigitalOceanConfigService $configService,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * 为请求设置API Key
     */
    private function prepareRequest($request): void
    {
        $config = $this->configService->getConfig();
        if ($config === null) {
            throw new \RuntimeException('未配置 DigitalOcean API Key');
        }

        $request->setApiKey($config->getApiKey());
    }

    /**
     * 获取SSH密钥ID列表
     */
    public function getSSHKeyIds(): array
    {
        $request = new ListSSHKeysRequest();
        $this->prepareRequest($request);

        try {
            $response = $this->client->request($request);
            $sshKeys = [];

            if (isset($response['ssh_keys']) && is_array($response['ssh_keys'])) {
                foreach ($response['ssh_keys'] as $key) {
                    if (isset($key['id'])) {
                        $sshKeys[] = $key['id'];
                    }
                }
            }

            return $sshKeys;
        } catch  (\Throwable $e) {
            $this->logger->error('获取DigitalOcean SSH密钥失败', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * 获取SSH密钥列表
     */
    public function listSSHKeys(int $page = 1, int $perPage = 20): array
    {
        $request = (new ListSSHKeysRequest())
            ->setPage($page)
            ->setPerPage($perPage);

        $this->prepareRequest($request);

        try {
            $response = $this->client->request($request);

            return [
                'ssh_keys' => $response['ssh_keys'] ?? [],
                'meta' => $response['meta'] ?? [],
                'links' => $response['links'] ?? [],
            ];
        } catch  (\Throwable $e) {
            $this->logger->error('获取DigitalOcean SSH密钥列表失败', [
                'error' => $e->getMessage(),
            ]);

            return ['ssh_keys' => [], 'meta' => [], 'links' => []];
        }
    }
}
