<?php

namespace DigitalOceanDropletBundle\Tests\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Entity\DigitalOceanConfig;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Service\DropletActionService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DropletActionServiceTest extends TestCase
{
    private DigitalOceanClient $client;
    private DigitalOceanConfigService $configService;
    private LoggerInterface $logger;
    private DropletActionService $service;
    private DigitalOceanConfig $config;

    protected function setUp(): void
    {
        $this->client = $this->createMock(DigitalOceanClient::class);
        $this->configService = $this->createMock(DigitalOceanConfigService::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->config = new DigitalOceanConfig();
        $this->config->setApiKey('test_api_key');
        
        $this->configService->expects($this->any())
            ->method('getConfig')
            ->willReturn($this->config);
        
        $this->service = new DropletActionService(
            $this->client,
            $this->configService,
            $this->logger
        );
    }

    public function testConstructorCreatesValidInstance(): void
    {
        $this->assertInstanceOf(DropletActionService::class, $this->service);
    }

    public function testPrepareRequestThrowsExceptionWhenConfigIsNull(): void
    {
        $configService = $this->createMock(DigitalOceanConfigService::class);
        $configService->expects($this->once())
            ->method('getConfig')
            ->willReturn(null);
        
        $service = new DropletActionService(
            $this->client,
            $configService,
            $this->logger
        );
        
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('未配置 DigitalOcean API Key');
        
        $service->listDropletActions(12345);
    }
    
    public function testRebootDropletLogsAction(): void
    {
        $dropletId = 12345;
        $expectedResponse = [
            'action' => [
                'id' => 123,
                'status' => 'in-progress',
                'type' => 'reboot'
            ]
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('重启'), $this->arrayHasKey('dropletId'));
        
        $result = $this->service->rebootDroplet($dropletId);
        $this->assertEquals($expectedResponse['action'], $result);
    }
    
    public function testPowerOffDropletLogsAction(): void
    {
        $dropletId = 12345;
        $expectedResponse = [
            'action' => [
                'id' => 123,
                'status' => 'in-progress',
                'type' => 'power_off'
            ]
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('关闭电源'), $this->arrayHasKey('dropletId'));
        
        $result = $this->service->powerOffDroplet($dropletId);
        $this->assertEquals($expectedResponse['action'], $result);
    }
    
    public function testWaitForActionCompletionSuccess(): void
    {
        $dropletId = 12345;
        $actionId = 123;
        $expectedStatus = ['completed'];
        $maxAttempts = 2;
        $interval = 0; // 设置为0以加速测试
        
        // 模拟 API 首次返回 in-progress，然后返回 completed
        $this->client->expects($this->exactly(2))
            ->method('request')
            ->willReturnOnConsecutiveCalls(
                [
                    'action' => [
                        'id' => $actionId,
                        'status' => 'in-progress',
                        'type' => 'reboot',
                    ]
                ],
                [
                    'action' => [
                        'id' => $actionId,
                        'status' => 'completed',
                        'type' => 'reboot',
                    ]
                ]
            );
        
        $result = $this->service->waitForActionCompletion($dropletId, $actionId, $expectedStatus, $maxAttempts, $interval);
        
        $this->assertIsArray($result);
        $this->assertEquals('completed', $result['status']);
    }
    
    public function testWaitForActionCompletionWithTimeout(): void
    {
        $dropletId = 12345;
        $actionId = 123;
        $expectedStatus = ['completed'];
        $maxAttempts = 2;
        $interval = 0; // 设置为0以加速测试
        
        // 模拟始终返回 in-progress
        $this->client->expects($this->exactly(2))
            ->method('request')
            ->willReturn([
                'action' => [
                    'id' => $actionId,
                    'status' => 'in-progress',
                    'type' => 'reboot',
                ]
            ]);
        
        $this->logger->expects($this->once())
            ->method('warning')
            ->with($this->stringContains('等待DigitalOcean虚拟机操作完成超时'), $this->arrayHasKey('dropletId'));
        
        $result = $this->service->waitForActionCompletion($dropletId, $actionId, $expectedStatus, $maxAttempts, $interval);
        
        $this->assertNull($result);
    }

    public function testListDropletActions(): void
    {
        $dropletId = 12345;
        $page = 1;
        $perPage = 20;
        
        $expectedResponse = [
            'actions' => [
                [
                    'id' => 123,
                    'status' => 'completed',
                    'type' => 'reboot',
                    'started_at' => '2023-01-01T00:00:00Z',
                    'completed_at' => '2023-01-01T00:01:00Z',
                ]
            ],
            'meta' => [
                'total' => 1
            ],
            'links' => []
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        $result = $this->service->listDropletActions($dropletId, $page, $perPage);
        
        $this->assertEquals($expectedResponse['actions'], $result['actions']);
        $this->assertEquals($expectedResponse['meta'], $result['meta']);
        $this->assertEquals($expectedResponse['links'], $result['links']);
    }
    
    public function testGetDropletAction(): void
    {
        $dropletId = 12345;
        $actionId = 123;
        
        $expectedResponse = [
            'action' => [
                'id' => $actionId,
                'status' => 'completed',
                'type' => 'reboot',
                'started_at' => '2023-01-01T00:00:00Z',
                'completed_at' => '2023-01-01T00:01:00Z',
            ]
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        $result = $this->service->getDropletAction($dropletId, $actionId);
        
        $this->assertEquals($expectedResponse['action'], $result);
    }
    
    /**
     * @dataProvider dropletOperationsProvider
     */
    public function testDropletOperations(string $methodName, string $actionType, array $additionalParams = []): void
    {
        $dropletId = 12345;
        
        $expectedResponse = [
            'action' => [
                'id' => 123,
                'status' => 'in-progress',
                'type' => $actionType,
                'started_at' => '2023-01-01T00:00:00Z',
                'completed_at' => null,
            ]
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        // 验证日志记录
        $this->logger->expects($this->once())
            ->method('info')
            ->with($this->stringContains('DigitalOcean虚拟机'), $this->arrayHasKey('dropletId'));
        
        // 构建方法参数
        $methodParams = [$dropletId];
        foreach ($additionalParams as $param) {
            $methodParams[] = $param;
        }
        
        $result = call_user_func_array([$this->service, $methodName], $methodParams);
        
        $this->assertEquals($expectedResponse['action'], $result);
    }
    
    public function dropletOperationsProvider(): array
    {
        return [
            'reboot' => ['rebootDroplet', 'reboot'],
            'power_off' => ['powerOffDroplet', 'power_off'],
            'power_on' => ['powerOnDroplet', 'power_on'],
            'shutdown' => ['shutdownDroplet', 'shutdown'],
            'enable_ipv6' => ['enableIpv6', 'enable_ipv6'],
            'enable_backups' => ['enableBackups', 'enable_backups'],
            'disable_backups' => ['disableBackups', 'disable_backups'],
            'reset_password' => ['resetPassword', 'password_reset'],
            'resize' => ['resizeDroplet', 'resize', ['s-2vcpu-2gb', true]],
            'rebuild' => ['rebuildDroplet', 'rebuild', [54321]],
            'rename' => ['renameDroplet', 'rename', ['new-name']],
            'snapshot' => ['snapshotDroplet', 'snapshot', ['snapshot-name']],
            'restore' => ['restoreDroplet', 'restore', [54321]],
        ];
    }
    
    /**
     * @dataProvider tagOperationsProvider
     */
    public function testDropletOperationsByTag(string $methodName, string $actionType, array $additionalParams = []): void
    {
        $tagName = 'web';
        
        $expectedResponse = [
            'actions' => [
                [
                    'id' => 123,
                    'status' => 'in-progress',
                    'type' => $actionType,
                    'started_at' => '2023-01-01T00:00:00Z',
                    'completed_at' => null,
                ]
            ]
        ];
        
        $this->client->expects($this->once())
            ->method('request')
            ->willReturn($expectedResponse);
        
        // 构建方法参数
        $methodParams = [$tagName];
        foreach ($additionalParams as $param) {
            $methodParams[] = $param;
        }
        
        $result = call_user_func_array([$this->service, $methodName], $methodParams);
        
        $this->assertEquals($expectedResponse['actions'], $result);
    }
    
    public function tagOperationsProvider(): array
    {
        return [
            'reboot_by_tag' => ['rebootDropletsByTag', 'reboot'],
            'power_off_by_tag' => ['powerOffDropletsByTag', 'power_off'],
            'power_on_by_tag' => ['powerOnDropletsByTag', 'power_on'],
            'snapshot_by_tag' => ['snapshotDropletsByTag', 'snapshot', ['snapshot-name']],
        ];
    }
} 