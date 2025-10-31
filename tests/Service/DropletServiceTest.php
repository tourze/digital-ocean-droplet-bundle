<?php

namespace DigitalOceanDropletBundle\Tests\Service;

use DigitalOceanAccountBundle\Entity\DigitalOceanConfig;
use DigitalOceanDropletBundle\Service\DropletService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(DropletService::class)]
#[RunTestsInSeparateProcesses]
final class DropletServiceTest extends AbstractIntegrationTestCase
{
    private DropletService $service;

    private DigitalOceanConfig $config;

    protected function onSetUp(): void
    {
        // 设置测试配置
        $this->config = new DigitalOceanConfig();
        $this->config->setApiKey('test_api_key');

        // 从容器获取服务
        $this->service = self::getService(DropletService::class);
    }

    public function testConstructService(): void
    {
        $this->assertInstanceOf(DropletService::class, $this->service);
    }

    /**
     * 测试 listDroplets 方法
     */
    public function testListDropletsThrowsExceptionWithoutValidConfig(): void
    {
        // 在集成测试中，期望抛出异常因为没有有效的API密钥
        $this->expectException(\Exception::class);
        $this->service->listDroplets();
    }

    /**
     * 测试 getDroplet 方法
     */
    public function testGetDropletThrowsExceptionWithoutValidConfig(): void
    {
        $dropletId = 12345;

        // 在集成测试中，期望抛出异常因为没有有效的API密钥
        $this->expectException(\Exception::class);
        $this->service->getDroplet($dropletId);
    }

    /**
     * 测试同步 Droplet 方法
     */
    public function testSyncDropletsThrowsExceptionWithoutValidConfig(): void
    {
        // 在集成测试中，期望抛出异常因为没有有效的API密钥
        $this->expectException(\Exception::class);
        $this->service->syncDroplets();
    }

    /**
     * 测试 createDroplet 方法
     */
    public function testCreateDropletThrowsExceptionWithoutValidConfig(): void
    {
        $name = 'test-droplet';
        $region = 'sgp1';
        $size = 's-1vcpu-1gb';
        $tags = ['web'];

        // 在集成测试中，期望抛出异常因为没有有效的API密钥
        $this->expectException(\Exception::class);
        $this->service->createDroplet($name, $region, $size, $tags);
    }

    /**
     * 测试 deleteDroplet 方法
     */
    public function testDeleteDropletReturnsFalseWithoutValidConfig(): void
    {
        $dropletId = 12345;

        // 在集成测试中，期望返回false因为删除方法会捕获异常并返回false
        $result = $this->service->deleteDroplet($dropletId);
        $this->assertFalse($result);
    }

    public function testWaitForDropletStatus(): void
    {
        self::markTestSkipped('需要Mock DigitalOcean客户端');
    }
}
