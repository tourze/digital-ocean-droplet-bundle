<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\GetDropletRequest;
use PHPUnit\Framework\TestCase;

class GetDropletRequestTest extends TestCase
{
    private const DROPLET_ID = 12345;
    private GetDropletRequest $request;
    
    protected function setUp(): void
    {
        $this->request = new GetDropletRequest(self::DROPLET_ID);
    }
    
    public function testConstructor(): void
    {
        $this->assertInstanceOf(GetDropletRequest::class, $this->request);
    }
    
    public function testGetRequestPath(): void
    {
        $this->assertEquals('/droplets/' . self::DROPLET_ID, $this->request->getRequestPath());
    }
    
    public function testGetRequestMethod(): void
    {
        $this->assertEquals('GET', $this->request->getRequestMethod());
    }
    
    public function testSetApiKey(): void
    {
        $apiKey = 'test-api-key';
        $result = $this->request->setApiKey($apiKey);
        
        $this->assertInstanceOf(GetDropletRequest::class, $result);
        $this->assertEquals($apiKey, $this->request->getApiKey());
    }
} 