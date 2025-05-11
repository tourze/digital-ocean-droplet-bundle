<?php

namespace DigitalOceanDropletBundle\Tests\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Entity\DigitalOceanConfig;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanAccountBundle\Service\SSHKeyService;
use DigitalOceanDropletBundle\Entity\Droplet;
use DigitalOceanDropletBundle\Repository\DropletRepository;
use DigitalOceanDropletBundle\Service\DropletService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DropletServiceTest extends TestCase
{
    private DigitalOceanClient $client;
    private DigitalOceanConfigService $configService;
    private EntityManagerInterface $entityManager;
    private DropletRepository $dropletRepository;
    private LoggerInterface $logger;
    private SSHKeyService $sshKeyService;
    private DropletService $service;
    private DigitalOceanConfig $config;

    protected function setUp(): void
    {
        $this->client = $this->createMock(DigitalOceanClient::class);
        $this->configService = $this->createMock(DigitalOceanConfigService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->dropletRepository = $this->createMock(DropletRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->sshKeyService = $this->createMock(SSHKeyService::class);
        
        $this->config = new DigitalOceanConfig();
        $this->config->setApiKey('test_api_key');
        
        $this->configService->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->config);
        
        $this->service = new DropletService(
            $this->client,
            $this->configService,
            $this->entityManager,
            $this->dropletRepository,
            $this->logger,
            $this->sshKeyService
        );
    }
    
    public function testConstructService(): void
    {
        $this->assertInstanceOf(DropletService::class, $this->service);
    }
    
    /**
     * 测试配置为空时抛出异常
     */
    public function testPrepareRequestThrowsExceptionWhenConfigIsNull(): void
    {
        $configService = $this->createMock(DigitalOceanConfigService::class);
        $configService->expects($this->once())
            ->method('getConfig')
            ->willReturn(null);
        
        $service = new DropletService(
            $this->client,
            $configService,
            $this->entityManager,
            $this->dropletRepository,
            $this->logger,
            $this->sshKeyService
        );
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('未配置 DigitalOcean API Key');
        
        $service->listDroplets();
    }
    
    /**
     * 测试 listDroplets 方法
     */
    public function testListDropletsReturnsFormattedResponse(): void
    {
        $expectedDroplets = [
            [
                'id' => 12345,
                'name' => 'test-droplet'
            ]
        ];
        
        $apiResponse = [
            'droplets' => $expectedDroplets,
            'meta' => ['total' => 1],
            'links' => []
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
        
        $result = $this->service->listDroplets();
        
        $this->assertEquals($expectedDroplets, $result['droplets']);
        $this->assertEquals(['total' => 1], $result['meta']);
        $this->assertEquals([], $result['links']);
    }
    
    /**
     * 测试 getDroplet 方法
     */
    public function testGetDropletReturnsFormattedResponse(): void
    {
        $dropletId = 12345;
        $expectedDroplet = [
            'id' => $dropletId,
            'name' => 'test-droplet'
        ];
        
        $apiResponse = [
            'droplet' => $expectedDroplet
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
        
        $result = $this->service->getDroplet($dropletId);
        
        $this->assertEquals($expectedDroplet, $result);
    }
    
    /**
     * 测试同步 Droplet 后 entityManager 的 persist 和 flush 是否被调用
     */
    public function testSyncDropletsCallsPersistAndFlush(): void
    {
        $dropletsData = [
            [
                'id' => 12345,
                'name' => 'test-droplet',
                'memory' => 2048,
                'vcpus' => 2,
                'disk' => 40,
                'region' => [
                    'slug' => 'sgp1'
                ],
                'status' => 'active',
                'image' => [
                    'id' => 123456,
                    'name' => 'Ubuntu 20.04'
                ]
            ]
        ];
        
        $expectedResponse = [
            'droplets' => $dropletsData,
            'meta' => [],
            'links' => []
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        $this->dropletRepository->expects($this->once())
            ->method('findOneBy')
            ->willReturn(null);
        
        $this->entityManager->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(Droplet::class));
        
        $this->entityManager->expects($this->once())
            ->method('flush');
        
        $result = $this->service->syncDroplets();
        
        $this->assertCount(1, $result);
        $this->assertInstanceOf(Droplet::class, $result[0]);
    }
    
    /**
     * 测试 createDroplet 方法
     */
    public function testCreateDropletReturnsFormattedResponse(): void
    {
        $name = 'test-droplet';
        $region = 'sgp1';
        $size = 's-1vcpu-1gb';
        $tags = ['web'];
        $sshKeys = [12345];
        
        $expectedDroplet = [
            'id' => 54321,
            'name' => $name,
            'region' => [
                'slug' => $region
            ],
            'size' => [
                'slug' => $size
            ],
            'tags' => $tags
        ];
        
        $apiResponse = [
            'droplet' => $expectedDroplet
        ];
        
        $this->sshKeyService->expects($this->once())
            ->method('getSSHKeyIds')
            ->willReturn($sshKeys);
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($apiResponse);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('DigitalOcean虚拟机创建请求已发送'), $this->arrayHasKey('name'));
        
        $result = $this->service->createDroplet($name, $region, $size, $tags);
        
        $this->assertEquals($expectedDroplet, $result);
    }
    
    /**
     * 测试 deleteDroplet 方法
     */
    public function testDeleteDropletReturnsTrue(): void
    {
        $dropletId = 12345;
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn([]);
        
        $result = $this->service->deleteDroplet($dropletId);
        
        $this->assertTrue($result);
    }
    
    /**
     * 测试 waitForDropletStatus 方法 - 成功场景
     */
    public function testWaitForDropletStatusReturnsExpectedStatus(): void
    {
        $dropletId = 12345;
        $expectedStatus = 'active';
        $maxAttempts = 2;
        $interval = 0; // 设置为0以加速测试
        $expectedIp = '192.168.1.1';
        
        // 模拟首次返回 new，然后返回 active 带网络信息
        $this->client->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                ['droplet' => ['status' => 'new']],
                [
                    'droplet' => [
                        'status' => 'active',
                        'networks' => [
                            'v4' => [
                                [
                                    'type' => 'public',
                                    'ip_address' => $expectedIp
                                ]
                            ]
                        ]
                    ]
                ]
            );
        
        $result = $this->service->waitForDropletStatus($dropletId, $expectedStatus, $maxAttempts, $interval);
        
        $this->assertEquals($expectedIp, $result);
    }
    
    /**
     * 测试 waitForDropletStatus 方法 - 超时场景
     */
    public function testWaitForDropletStatusTimesOutAndReturnsNull(): void
    {
        $dropletId = 12345;
        $expectedStatus = 'active';
        $currentStatus = 'new';
        $maxAttempts = 2;
        $interval = 0; // 设置为0以加速测试
        
        // 模拟始终返回 new
        $this->client->expects($this->exactly($maxAttempts))
            ->method('request')
            ->willReturn(['droplet' => ['status' => $currentStatus]]);
        
        $this->logger->expects($this->exactly($maxAttempts))
            ->method('info')
            ->with($this->stringContains('等待DigitalOcean虚拟机激活'), $this->arrayHasKey('dropletId'));
        
        $this->logger->expects($this->once())
            ->method('error')
            ->with($this->stringContains('等待DigitalOcean虚拟机激活超时'), $this->arrayHasKey('dropletId'));
        
        $result = $this->service->waitForDropletStatus($dropletId, $expectedStatus, $maxAttempts, $interval);
        
        $this->assertNull($result);
    }
} 