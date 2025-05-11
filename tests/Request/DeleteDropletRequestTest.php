<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\DeleteDropletRequest;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class DeleteDropletRequestTest extends TestCase
{
    private const DROPLET_ID = 12345;
    private DeleteDropletRequest $request;

    protected function setUp(): void
    {
        $this->request = new DeleteDropletRequest(self::DROPLET_ID);
    }

    public function testConstructor(): void
    {
        $this->assertInstanceOf(DeleteDropletRequest::class, $this->request);
    }

    public function testGetRequestPath(): void
    {
        $this->assertEquals('/droplets/' . self::DROPLET_ID, $this->request->getRequestPath());
    }

    public function testGetRequestMethod(): void
    {
        // 使用反射获取私有属性 $method
        $reflection = new ReflectionClass($this->request);
        $property = $reflection->getProperty('method');
        $property->setAccessible(true);
        $method = $property->getValue($this->request);

        $this->assertEquals('DELETE', $method);
    }

    public function testSetApiKey(): void
    {
        $apiKey = 'test-api-key';
        $result = $this->request->setApiKey($apiKey);

        $this->assertInstanceOf(DeleteDropletRequest::class, $result);
        $this->assertEquals($apiKey, $this->request->getApiKey());
    }
}
