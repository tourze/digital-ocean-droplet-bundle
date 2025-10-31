<?php

namespace DigitalOceanDropletBundle\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Request\DigitalOceanRequest;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Entity\Droplet;
use DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException;
use DigitalOceanDropletBundle\Repository\DropletRepository;
use DigitalOceanDropletBundle\Request\CreateDropletRequest;
use DigitalOceanDropletBundle\Request\DeleteDropletRequest;
use DigitalOceanDropletBundle\Request\GetDropletRequest;
use DigitalOceanDropletBundle\Request\ListDropletsRequest;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Tourze\Symfony\AopDoctrineBundle\Attribute\Transactional;

#[Autoconfigure(public: true)]
#[WithMonologChannel(channel: 'digital_ocean_droplet')]
readonly class DropletService
{
    public function __construct(
        private DigitalOceanClient $client,
        private DigitalOceanConfigService $configService,
        private EntityManagerInterface $entityManager,
        private DropletRepository $dropletRepository,
        private LoggerInterface $logger,
        private SSHKeyService $sshKeyService,
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
     * 获取虚拟机列表
     *
     * @return array<string, mixed>
     */
    public function listDroplets(int $page = 1, int $perPage = 20, ?string $tagName = null): array
    {
        $request = new ListDropletsRequest();
        $request->setPage($page);
        $request->setPerPage($perPage);

        if (null !== $tagName) {
            $request->setTagName($tagName);
        }

        $this->prepareRequest($request);

        $response = $this->client->request($request);
        assert(is_array($response), 'API response must be an array');

        return [
            'droplets' => $response['droplets'] ?? [],
            'meta' => $response['meta'] ?? [],
            'links' => $response['links'] ?? [],
        ];
    }

    /**
     * 获取单个虚拟机信息
     *
     * @return array<string, mixed>
     */
    public function getDroplet(int $dropletId): array
    {
        $request = new GetDropletRequest($dropletId);
        $this->prepareRequest($request);

        $response = $this->client->request($request);
        assert(is_array($response), 'API response must be an array');

        /** @var array<string, mixed> $droplet */
        $droplet = $response['droplet'] ?? [];

        return $droplet;
    }

    /**
     * 同步所有虚拟机到数据库
     *
     * @return array<Droplet>
     */
    #[Transactional]
    public function syncDroplets(): array
    {
        $dropletsData = $this->listDroplets(1, 100)['droplets'] ?? [];
        assert(is_array($dropletsData), 'Droplets data must be an array');

        if ([] === $dropletsData) {
            $this->logger->info('没有找到任何虚拟机');

            return [];
        }

        $droplets = [];

        foreach ($dropletsData as $dropletData) {
            if (!is_array($dropletData)) {
                continue;
            }

            /** @var int $dropletId */
            $dropletId = $dropletData['id'] ?? 0;

            if (0 === $dropletId) {
                continue;
            }

            /** @var array<string, mixed> $validDropletData */
            $validDropletData = $dropletData;

            $droplet = $this->syncSingleDroplet($validDropletData, $dropletId);
            $droplets[] = $droplet;
        }

        $this->entityManager->flush();

        $this->logger->info('DigitalOcean虚拟机已同步', ['count' => count($droplets)]);

        return $droplets;
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function syncSingleDroplet(array $dropletData, int $dropletId): Droplet
    {
        $droplet = $this->dropletRepository->findOneBy(['dropletId' => $dropletId]) ?? new Droplet();

        $this->updateDropletBasicInfo($droplet, $dropletData, $dropletId);
        $this->updateDropletImageInfo($droplet, $dropletData);
        $this->updateDropletOptionalInfo($droplet, $dropletData);
        $this->updateDropletCreatedAt($droplet, $dropletData, $dropletId);

        $this->entityManager->persist($droplet);

        return $droplet;
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function updateDropletBasicInfo(Droplet $droplet, array $dropletData, int $dropletId): void
    {
        $droplet->setDropletId($dropletId);

        /** @var string $name */
        $name = $dropletData['name'] ?? '';
        $droplet->setName($name);

        /** @var int|string $memory */
        $memory = $dropletData['memory'] ?? '';
        $droplet->setMemory((string) $memory);

        /** @var int|string $vcpus */
        $vcpus = $dropletData['vcpus'] ?? '';
        $droplet->setVcpus((string) $vcpus);

        /** @var int|string $disk */
        $disk = $dropletData['disk'] ?? '';
        $droplet->setDisk((string) $disk);

        /** @var string $region */
        $region = isset($dropletData['region']) && is_array($dropletData['region']) ? ($dropletData['region']['slug'] ?? '') : '';
        $droplet->setRegion($region);

        /** @var string $status */
        $status = $dropletData['status'] ?? '';
        $droplet->setStatus($status);
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function updateDropletImageInfo(Droplet $droplet, array $dropletData): void
    {
        if (isset($dropletData['image']) && is_array($dropletData['image'])) {
            /** @var int|string $imageId */
            $imageId = $dropletData['image']['id'] ?? '';
            $droplet->setImageId((string) $imageId);

            /** @var string|null $imageName */
            $imageName = $dropletData['image']['name'] ?? null;
            $droplet->setImageName($imageName);
        }
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function updateDropletOptionalInfo(Droplet $droplet, array $dropletData): void
    {
        if (isset($dropletData['networks'])) {
            /** @var array<string, mixed>|null $networks */
            $networks = is_array($dropletData['networks']) ? $dropletData['networks'] : null;
            $droplet->setNetworks($networks);
        }

        if (isset($dropletData['tags'])) {
            /** @var array<string>|null $tags */
            $tags = is_array($dropletData['tags']) ? $dropletData['tags'] : null;
            $droplet->setTags($tags);
        }

        if (isset($dropletData['volume_ids'])) {
            /** @var array<string>|null $volumeIds */
            $volumeIds = is_array($dropletData['volume_ids']) ? $dropletData['volume_ids'] : null;
            $droplet->setVolumeIds($volumeIds);
        }
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function updateDropletCreatedAt(Droplet $droplet, array $dropletData, int $dropletId): void
    {
        if (!isset($dropletData['created_at'])) {
            return;
        }

        /** @var string $createdAt */
        $createdAt = $dropletData['created_at'];

        try {
            $droplet->setCreateTime(new \DateTimeImmutable($createdAt));
        } catch (\Throwable $e) {
            $this->logger->warning('无法解析虚拟机创建时间', [
                'dropletId' => $dropletId,
                'created_at' => $createdAt,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 创建虚拟机
     *
     * @param array<string> $tags
     * @return array<string, mixed>
     */
    public function createDroplet(string $name, string $region = 'sgp1', string $size = 's-1vcpu-1gb', array $tags = []): array
    {
        $request = new CreateDropletRequest();
        $request->setName($name);
        $request->setRegion($region);
        $request->setSize($size);

        foreach ($tags as $tag) {
            $request->addTag($tag);
        }

        // 添加SSH密钥
        $sshKeys = $this->sshKeyService->getSSHKeyIds();
        if ([] !== $sshKeys) {
            $request->setSshKeys($sshKeys);
        }

        $this->prepareRequest($request);

        $response = $this->client->request($request);
        assert(is_array($response), 'API response must be an array');

        $this->logger->info('DigitalOcean虚拟机创建请求已发送', [
            'name' => $name,
            'region' => $region,
            'size' => $size,
            'tags' => $tags,
        ]);

        /** @var array<string, mixed> $droplet */
        $droplet = $response['droplet'] ?? [];

        return $droplet;
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
        } catch (\Throwable $e) {
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
     * @param int    $dropletId      Droplet ID
     * @param string $expectedStatus 期望的状态，默认为 active
     * @param int    $maxAttempts    最大尝试次数
     * @param int    $interval       检查间隔（秒）
     *
     * @return string|null 公网IP地址，如果未激活则返回null
     */
    public function waitForDropletStatus(int $dropletId, string $expectedStatus = 'active', int $maxAttempts = 60, int $interval = 10): ?string
    {
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            $dropletData = $this->checkDropletStatus($dropletId, $attempt);
            if (null === $dropletData) {
                sleep($interval);
                ++$attempt;
                continue;
            }

            /** @var string $status */
            $status = $dropletData['status'] ?? '';
            if ($status === $expectedStatus) {
                return $this->extractPublicIpAddress($dropletData, $dropletId);
            }

            $this->logWaitingStatus($dropletId, $status, $expectedStatus, $attempt, $maxAttempts);
            sleep($interval);
            ++$attempt;
        }

        $this->logTimeout($dropletId, $expectedStatus, $maxAttempts);

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function checkDropletStatus(int $dropletId, int $attempt): ?array
    {
        try {
            return $this->getDroplet($dropletId);
        } catch (\Throwable $e) {
            $this->logger->error('检查DigitalOcean虚拟机状态失败', [
                'dropletId' => $dropletId,
                'error' => $e->getMessage(),
                'attempt' => $attempt + 1,
            ]);

            return null;
        }
    }

    /**
     * @param array<string, mixed> $dropletData
     */
    private function extractPublicIpAddress(array $dropletData, int $dropletId): ?string
    {
        if (!isset($dropletData['networks']) || !is_array($dropletData['networks'])) {
            $this->logger->warning('DigitalOcean虚拟机已激活但找不到网络信息', [
                'dropletId' => $dropletId,
            ]);

            return null;
        }

        if (!isset($dropletData['networks']['v4']) || !is_array($dropletData['networks']['v4'])) {
            $this->logger->warning('DigitalOcean虚拟机已激活但找不到公网IP', [
                'dropletId' => $dropletId,
            ]);

            return null;
        }

        foreach ($dropletData['networks']['v4'] as $network) {
            if (!is_array($network)) {
                continue;
            }

            if (isset($network['type']) && 'public' === $network['type'] && isset($network['ip_address'])) {
                /** @var string $ipAddress */
                $ipAddress = $network['ip_address'];

                return $ipAddress;
            }
        }

        $this->logger->warning('DigitalOcean虚拟机已激活但找不到公网IP', [
            'dropletId' => $dropletId,
        ]);

        return null;
    }

    private function logWaitingStatus(int $dropletId, string $currentStatus, string $expectedStatus, int $attempt, int $maxAttempts): void
    {
        $this->logger->info('等待DigitalOcean虚拟机激活', [
            'dropletId' => $dropletId,
            'currentStatus' => $currentStatus,
            'expectedStatus' => $expectedStatus,
            'attempt' => $attempt + 1,
            'maxAttempts' => $maxAttempts,
        ]);
    }

    private function logTimeout(int $dropletId, string $expectedStatus, int $maxAttempts): void
    {
        $this->logger->error('等待DigitalOcean虚拟机激活超时', [
            'dropletId' => $dropletId,
            'expectedStatus' => $expectedStatus,
            'attempts' => $maxAttempts,
        ]);
    }
}
