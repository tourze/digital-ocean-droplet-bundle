<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\ListDropletActionsRequest;
use PHPUnit\Framework\TestCase;

class ListDropletActionsRequestTest extends TestCase
{
    private const DROPLET_ID = 12345;
    private ListDropletActionsRequest $request;
    
    protected function setUp(): void
    {
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
        $result = $this->request->setApiKey($apiKey);
        
        $this->assertInstanceOf(ListDropletActionsRequest::class, $result);
        $this->assertEquals($apiKey, $this->request->getApiKey());
    }
    
    public function testFluidInterface(): void
    {
        $this->assertInstanceOf(
            ListDropletActionsRequest::class,
            $this->request->setPage(2)
                ->setPerPage(50)
                ->setApiKey('test-api-key')
        );
    }
} 