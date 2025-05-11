<?php

namespace DigitalOceanDropletBundle\Tests\Request;

use DigitalOceanDropletBundle\Request\ListDropletsRequest;
use PHPUnit\Framework\TestCase;

class ListDropletsRequestTest extends TestCase
{
    private ListDropletsRequest $request;
    
    protected function setUp(): void
    {
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
        $this->assertEquals($page, $options['query']['page']);
    }
    
    public function testSetPerPage(): void
    {
        $perPage = 50;
        $this->request->setPerPage($perPage);
        
        $options = $this->request->getRequestOptions();
        $this->assertEquals($perPage, $options['query']['per_page']);
    }
    
    public function testSetTagName(): void
    {
        $tagName = 'web-servers';
        $this->request->setTagName($tagName);
        
        $options = $this->request->getRequestOptions();
        $this->assertEquals($tagName, $options['query']['tag_name']);
    }
    
    public function testSetApiKey(): void
    {
        $apiKey = 'test-api-key';
        $result = $this->request->setApiKey($apiKey);
        
        $this->assertInstanceOf(ListDropletsRequest::class, $result);
        $this->assertEquals($apiKey, $this->request->getApiKey());
    }
    
    public function testFluidInterface(): void
    {
        $this->assertInstanceOf(
            ListDropletsRequest::class,
            $this->request->setPage(2)
                ->setPerPage(50)
                ->setTagName('web-servers')
                ->setApiKey('test-api-key')
        );
    }
} 