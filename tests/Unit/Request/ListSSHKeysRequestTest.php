<?php

namespace DigitalOceanDropletBundle\Tests\Unit\Request;

use DigitalOceanDropletBundle\Request\ListSSHKeysRequest;
use PHPUnit\Framework\TestCase;

class ListSSHKeysRequestTest extends TestCase
{
    public function testGetRequestPath(): void
    {
        $request = new ListSSHKeysRequest();
        $this->assertStringContainsString('/account/keys', $request->getRequestPath());
    }

    public function testSetPage(): void
    {
        $request = new ListSSHKeysRequest();
        $result = $request->setPage(2);
        
        $this->assertSame($request, $result);
        $this->assertStringContainsString('page=2', $request->getRequestPath());
    }

    public function testSetPerPage(): void
    {
        $request = new ListSSHKeysRequest();
        $result = $request->setPerPage(50);
        
        $this->assertSame($request, $result);
        $this->assertStringContainsString('per_page=50', $request->getRequestPath());
    }
}