<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\ListSSHKeysRequest;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ListSSHKeysRequest::class)]
final class ListSSHKeysRequestTest extends RequestTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // 测试设置
    }

    public function testGetRequestPath(): void
    {
        $request = new ListSSHKeysRequest();
        $this->assertStringContainsString('/account/keys', $request->getRequestPath());
    }

    public function testSetPage(): void
    {
        $request = new ListSSHKeysRequest();
        $request->setPage(2);

        // setPage 现在返回void，不再返回对象实例
        // 直接验证设置效果
        $this->assertStringContainsString('page=2', $request->getRequestPath());
    }

    public function testSetPerPage(): void
    {
        $request = new ListSSHKeysRequest();
        $request->setPerPage(50);

        // setPerPage 现在返回void，不再返回对象实例
        // 直接验证设置效果
        $this->assertStringContainsString('per_page=50', $request->getRequestPath());
    }
}
