<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\ListDropletsRequest;
use HttpClientBundle\Tests\Request\RequestTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(ListDropletsRequest::class)]
final class ListDropletsRequestTest extends RequestTestCase
{
    private ListDropletsRequest $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->request = new ListDropletsRequest();
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/droplets', $this->request->getRequestPath());
    }

    public function testSetPage(): void
    {
        $page = 2;
        $this->request->setPage($page);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertEquals($page, $options['query']['page']);
    }

    public function testSetPerPage(): void
    {
        $perPage = 50;
        $this->request->setPerPage($perPage);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertEquals($perPage, $options['query']['per_page']);
    }

    public function testSetTagName(): void
    {
        $tagName = 'web-servers';
        $this->request->setTagName($tagName);

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertEquals($tagName, $options['query']['tag_name']);
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
        $this->request->setTagName('web-servers');

        // 验证设置正确应用
        $this->assertEquals('test-api-key', $this->request->getApiKey());

        $options = $this->request->getRequestOptions();
        $this->assertNotNull($options);
        $this->assertArrayHasKey('query', $options);
        $this->assertIsArray($options['query']);
        $this->assertEquals(2, $options['query']['page']);
        $this->assertEquals(50, $options['query']['per_page']);
        $this->assertEquals('web-servers', $options['query']['tag_name']);
    }
}
