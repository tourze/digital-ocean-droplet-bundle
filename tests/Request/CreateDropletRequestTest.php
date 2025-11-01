<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\CreateDropletRequest;
use HttpClientBundle\Test\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(CreateDropletRequest::class)]
final class CreateDropletRequestTest extends RequestTestCase
{
    private CreateDropletRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new CreateDropletRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/droplets', $this->request->getRequestPath());
    }

    public function testSetName(): void
    {
        $name = 'test-droplet';
        $this->request->setName($name);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($name, $options['name']);
    }

    public function testSetRegion(): void
    {
        $region = 'nyc1';
        $this->request->setRegion($region);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($region, $options['region']);
    }

    public function testSetSize(): void
    {
        $size = 's-2vcpu-2gb';
        $this->request->setSize($size);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($size, $options['size']);
    }

    public function testSetImage(): void
    {
        $image = 'ubuntu-20-04-x64';
        $this->request->setImage($image);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($image, $options['image']);
    }

    public function testSetSshKeys(): void
    {
        $sshKeys = ['12345', '67890'];
        $this->request->setSshKeys($sshKeys);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($sshKeys, $options['ssh_keys']);
    }

    public function testSetBackups(): void
    {
        $backups = true;
        $this->request->setBackups($backups);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($backups, $options['backups']);
    }

    public function testSetIpv6(): void
    {
        $ipv6 = true;
        $this->request->setIpv6($ipv6);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($ipv6, $options['ipv6']);
    }

    public function testSetMonitoring(): void
    {
        $monitoring = false;
        $this->request->setMonitoring($monitoring);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($monitoring, $options['monitoring']);
    }

    public function testSetTags(): void
    {
        $tags = ['web', 'production'];
        $this->request->setTags($tags);

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals($tags, $options['tags']);
    }

    public function testAddTag(): void
    {
        $this->request->setTags(['web']);
        $this->request->addTag('production');

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals(['web', 'production'], $options['tags']);
    }

    public function testAddTagDoesNotDuplicate(): void
    {
        $this->request->setTags(['web']);
        $this->request->addTag('web');

        $options = $this->getRequestOptionsPayload();
        $this->assertEquals(['web'], $options['tags']);
    }

    public function testFluidInterface(): void
    {
        // 所有setter方法现在返回void，不支持链式调用
        // 改为分别调用并验证最终状态
        $this->request->setName('test');
        $this->request->setRegion('nyc1');
        $this->request->setSize('s-1vcpu-1gb');
        $this->request->setImage('ubuntu-20-04-x64');
        $this->request->setSshKeys(['12345']);
        $this->request->setBackups(true);
        $this->request->setIpv6(true);
        $this->request->setMonitoring(true);
        $this->request->setTags(['web']);
        $this->request->addTag('production');

        // 验证所有设置都正确应用
        $options = $this->getRequestOptionsPayload();
        $this->assertEquals('test', $options['name']);
        $this->assertEquals('nyc1', $options['region']);
        $this->assertEquals('s-1vcpu-1gb', $options['size']);
        $this->assertEquals('ubuntu-20-04-x64', $options['image']);
        $this->assertEquals([12345], $options['ssh_keys']);
        $this->assertTrue($options['backups']);
        $this->assertTrue($options['ipv6']);
        $this->assertTrue($options['monitoring']);
        $this->assertEquals(['web', 'production'], $options['tags']);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRequestOptionsPayload(): array
    {
        $reflection = new \ReflectionClass($this->request);
        $property = $reflection->getProperty('payload');
        $property->setAccessible(true);

        /** @var array<string, mixed> */
        return $property->getValue($this->request);
    }
}
