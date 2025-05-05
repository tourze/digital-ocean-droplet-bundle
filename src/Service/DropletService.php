<?php

namespace DigitalOceanDropletBundle\Service;

use AopDoctrineBundle\Attribute\Transactional;
use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanAccountBundle\Service\SSHKeyService;
use DigitalOceanDropletBundle\Entity\Droplet;
use DigitalOceanDropletBundle\Repository\DropletRepository;
use DigitalOceanDropletBundle\Request\CreateDropletRequest;
use DigitalOceanDropletBundle\Request\DeleteDropletRequest;
use DigitalOceanDropletBundle\Request\GetDropletRequest;
use DigitalOceanDropletBundle\Request\ListDropletsRequest;
use DoctrineEnhanceBundle\Service\EntityManager;
use Psr\Log\LoggerInterface;

class DropletService
{
    public function __construct(
        private readonly DigitalOceanClient $client,
        private readonly DigitalOceanConfigService $configService,
        private readonly EntityManager $entityManager,
        private readonly DropletRepository $dropletRepository,
        private readonly LoggerInterface $logger,
        private readonly SSHKeyService $sshKeyService,
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
     * 获取虚拟机列表
     */
    public function listDroplets(int $page = 1, int $perPage = 20, ?string $tagName = null): array
    {
        $request = (new ListDropletsRequest())
            ->setPage($page)
            ->setPerPage($perPage);

        if ($tagName !== null) {
            $request->setTagName($tagName);
        }

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        return [
            'droplets' => $response['droplets'] ?? [],
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * 获取单个虚拟机信息
     */
    public function getDroplet(int $dropletId): array
    {
        $request = new GetDropletRequest($dropletId);
        $this->prepareRequest($request);

        $response = $this->client->request($request);

        return $response['droplet'] ?? [];
    }

    /**
     * 同步所有虚拟机到数据库
     */
    #[Transactional]
    public function syncDroplets(): array
    {
        $dropletsData = $this->listDroplets(1, 100)['droplets'] ?? [];

        if (empty($dropletsData)) {
            $this->logger->info('没有找到任何虚拟机');
            return [];
        }

        $droplets = [];

        foreach ($dropletsData as $dropletData) {
            $dropletId = $dropletData['id'] ?? 0;

            if ($dropletId === 0) {
                continue;
            }

            // 查找现有虚拟机或创建新虚拟机
            $droplet = $this->dropletRepository->findOneBy(['dropletId' => $dropletId]) ?? new Droplet();

            // 更新虚拟机信息
            $droplet->setDropletId($dropletId)
                ->setName($dropletData['name'] ?? '')
                ->setMemory((string)($dropletData['memory'] ?? ''))
                ->setVcpus((string)($dropletData['vcpus'] ?? ''))
                ->setDisk((string)($dropletData['disk'] ?? ''))
                ->setRegion($dropletData['region']['slug'] ?? '')
                ->setStatus($dropletData['status'] ?? '');

            // 更新镜像信息
            if (isset($dropletData['image'])) {
                $droplet->setImageId((string)($dropletData['image']['id'] ?? ''))
                    ->setImageName($dropletData['image']['name'] ?? null);
            }

            // 更新网络信息
            if (isset($dropletData['networks'])) {
                $droplet->setNetworks($dropletData['networks']);
            }

            // 更新标签
            if (isset($dropletData['tags'])) {
                $droplet->setTags($dropletData['tags']);
            }

            // 更新卷IDs
            if (isset($dropletData['volume_ids'])) {
                $droplet->setVolumeIds($dropletData['volume_ids']);
            }

            // 更新创建时间
            if (isset($dropletData['created_at'])) {
                try {
                    $droplet->setCreatedAt(new \DateTimeImmutable($dropletData['created_at']));
                } catch (\Exception $e) {
                    $this->logger->warning('无法解析虚拟机创建时间', [
                        'dropletId' => $dropletId,
                        'created_at' => $dropletData['created_at'],
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $this->entityManager->persist($droplet);
            $droplets[] = $droplet;
        }

        $this->entityManager->flush();

        $this->logger->info('DigitalOcean虚拟机已同步', ['count' => count($droplets)]);

        return $droplets;
    }

    /**
     * 创建虚拟机
     */
    public function createDroplet(string $name, string $region = 'sgp1', string $size = 's-1vcpu-1gb', array $tags = []): array
    {
        $request = (new CreateDropletRequest())
            ->setName($name)
            ->setRegion($region)
            ->setSize($size);

        foreach ($tags as $tag) {
            $request->addTag($tag);
        }

        // 添加SSH密钥
        $sshKeys = $this->sshKeyService->getSSHKeyIds();
        if (!empty($sshKeys)) {
            $request->setSshKeys($sshKeys);
        }

        $this->prepareRequest($request);

        $response = $this->client->request($request);

        $this->logger->info('DigitalOcean虚拟机创建请求已发送', [
            'name' => $name,
            'region' => $region,
            'size' => $size,
            'tags' => $tags,
        ]);

        return $response['droplet'] ?? [];
    }

    /**
     * 删除虚拟机
     */
    public function deleteDroplet(int $dropletId): bool
    {
        $request = new DeleteDropletRequest($dropletId);
        $this->prepareRequest($request);

        try {
            $response = $this->client->request($request);

            // 删除成功通常返回204状态码
            $this->logger->info('DigitalOcean虚拟机删除成功', [
                'dropletId' => $dropletId,
            ]);

            return true;
        } catch (\Exception $e) {
            $this->logger->error('DigitalOcean虚拟机删除失败', [
                'dropletId' => $dropletId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * 等待Droplet进入指定状态，返回IP地址
     *
     * @param int $dropletId Droplet ID
     * @param string $expectedStatus 期望的状态，默认为 active
     * @param int $maxAttempts 最大尝试次数
     * @param int $interval 检查间隔（秒）
     * @return string|null 公网IP地址，如果未激活则返回null
     */
    public function waitForDropletStatus(int $dropletId, string $expectedStatus = 'active', int $maxAttempts = 60, int $interval = 10): ?string
    {
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                $dropletData = $this->getDroplet($dropletId);
                $status = $dropletData['status'] ?? '';

                if ($status === $expectedStatus) {
                    // 获取公网IP
                    if (isset($dropletData['networks']['v4']) && is_array($dropletData['networks']['v4'])) {
                        foreach ($dropletData['networks']['v4'] as $network) {
                            if (isset($network['type']) && $network['type'] === 'public' && isset($network['ip_address'])) {
                                return $network['ip_address'];
                            }
                        }
                    }

                    // 找不到公网IP
                    $this->logger->warning('DigitalOcean虚拟机已激活但找不到公网IP', [
                        'dropletId' => $dropletId,
                    ]);

                    return null;
                }

                $this->logger->info('等待DigitalOcean虚拟机激活', [
                    'dropletId' => $dropletId,
                    'currentStatus' => $status,
                    'expectedStatus' => $expectedStatus,
                    'attempt' => $attempt + 1,
                    'maxAttempts' => $maxAttempts,
                ]);
            } catch (\Exception $e) {
                $this->logger->error('检查DigitalOcean虚拟机状态失败', [
                    'dropletId' => $dropletId,
                    'error' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);
            }

            sleep($interval);
            $attempt++;
        }

        $this->logger->error('等待DigitalOcean虚拟机激活超时', [
            'dropletId' => $dropletId,
            'expectedStatus' => $expectedStatus,
            'attempts' => $maxAttempts,
        ]);

        return null;
    }
}
