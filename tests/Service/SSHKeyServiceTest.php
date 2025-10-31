<?php

namespace DigitalOceanDropletBundle\Tests\Service;

use DigitalOceanAccountBundle\Entity\DigitalOceanConfig;
use DigitalOceanDropletBundle\Service\SSHKeyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(SSHKeyService::class)]
#[RunTestsInSeparateProcesses]
final class SSHKeyServiceTest extends AbstractIntegrationTestCase
{
    private SSHKeyService $service;

    private DigitalOceanConfig $config;

    protected function onSetUp(): void
    {
        // 设置测试配置
        $this->config = new DigitalOceanConfig();
        $this->config->setApiKey('test_api_key');

        // 从容器获取服务
        $this->service = self::getService(SSHKeyService::class);
    }

    public function testServiceInstanceCreation(): void
    {
        $this->assertInstanceOf(SSHKeyService::class, $this->service);
    }

    public function testListSSHKeys(): void
    {
        // 在集成测试中，期望返回空数组因为方法会捕获异常并返回默认值
        $result = $this->service->listSSHKeys(1, 20);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('ssh_keys', $result);
        $this->assertArrayHasKey('meta', $result);
        $this->assertArrayHasKey('links', $result);
        $this->assertEquals([], $result['ssh_keys']);
        $this->assertEquals([], $result['meta']);
        $this->assertEquals([], $result['links']);
    }
}
