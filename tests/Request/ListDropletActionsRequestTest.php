<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\ListDropletActionsRequest;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ListDropletActionsRequest::class)]
final class ListDropletActionsRequestTest extends RequestTestCase
{
    private const DROPLET_ID = 12345;

    private ListDropletActionsRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new ListDropletActionsRequest(self::DROPLET_ID);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(ListDropletActionsRequest::class, $this->request);
    }

    public function testGetRequestPath(): void
    {
        $path = $this->request->getRequestPath();
        $this->assertStringStartsWith('/droplets/' . self::DROPLET_ID . '/actions?', $path);
        $this->assertStringContainsString('page=1', $path);
        $this->assertStringContainsString('per_page=20', $path);
    }

    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }

    public function testSetPage(): void
    {
        $page = 2;
        $this->request->setPage($page);

        $path = $this->request->getRequestPath();
        $this->assertStringContainsString('page=2', $path);
    }

    public function testSetPerPage(): void
    {
        $perPage = 50;
        $this->request->setPerPage($perPage);

        $path = $this->request->getRequestPath();
        $this->assertStringContainsString('per_page=50', $path);
    }

    public function testSetApiKey(): void
    {
        $apiKey = 'test-api-key';
        $this->request->setApiKey($apiKey);

        $this->assertEquals($apiKey, $this->request->getApiKey());
    }

    public function testFluidInterface(): void
    {
        // 所有setter方法现在返回void，不支持链式调用
        // 改为分别调用并验证最终状态
        $this->request->setApiKey('test-api-key');
        $this->request->setPage(2);
        $this->request->setPerPage(50);

        // 验证设置正确应用
        $this->assertEquals('test-api-key', $this->request->getApiKey());

        $path = $this->request->getRequestPath();
        $this->assertStringContainsString('page=2', $path);
        $this->assertStringContainsString('per_page=50', $path);
    }
}
