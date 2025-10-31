<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Request\DigitalOceanRequest;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException;
use DigitalOceanDropletBundle\Request\ListSSHKeysRequest;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;

#[WithMonologChannel(channel: 'digital_ocean_droplet')]
readonly class SSHKeyService
{
    public function __construct(
        private DigitalOceanClient $client,
        private DigitalOceanConfigService $configService,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * 为请求设置API Key
     */
    private function prepareRequest(DigitalOceanRequest $request): void
    {
        $config = $this->configService->getConfig();
        if (null === $config) {
            throw new DigitalOceanConfigurationException('未配置 DigitalOcean API Key');
        }

        $request->setApiKey($config->getApiKey());
    }

    /**
     * 获取SSH密钥ID列表
     *
     * @return array<string>
     */
    public function getSSHKeyIds(): array
    {
        $request = new ListSSHKeysRequest();
        $this->prepareRequest($request);

        try {
            $response = $this->client->request($request);
            if (!is_array($response)) {
                $this->logger->error('DigitalOcean 返回值异常', ['response' => $response]);

                return [];
            }
            /** @var array<string, mixed> $response */

            $sshKeys = [];

            if (isset($response['ssh_keys']) && is_array($response['ssh_keys'])) {
                foreach ($response['ssh_keys'] as $key) {
                    if (!is_array($key) || !isset($key['id'])) {
                        continue;
                    }
                    $id = $key['id'];
                    if (!is_scalar($id)) {
                        continue;
                    }
                    $sshKeys[] = (string) $id;
                }
            }

            return $sshKeys;
        } catch (\Throwable $e) {
            $this->logger->error('获取DigitalOcean SSH密钥失败', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * 获取SSH密钥列表
     *
     * @return array<string, mixed>
     */
    public function listSSHKeys(int $page = 1, int $perPage = 20): array
    {
        $request = new ListSSHKeysRequest();
        $request->setPage($page);
        $request->setPerPage($perPage);

        $this->prepareRequest($request);

        try {
            $response = $this->client->request($request);
            if (!is_array($response)) {
                $this->logger->error('DigitalOcean 返回值异常', ['response' => $response]);

                return ['ssh_keys' => [], 'meta' => [], 'links' => []];
            }
            /** @var array<string, mixed> $response */

            return [
                'ssh_keys' => $response['ssh_keys'] ?? [],
                'meta' => $response['meta'] ?? [],
                'links' => $response['links'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->logger->error('获取DigitalOcean SSH密钥列表失败', [
                'error' => $e->getMessage(),
            ]);

            return ['ssh_keys' => [], 'meta' => [], 'links' => []];
        }
    }
}
