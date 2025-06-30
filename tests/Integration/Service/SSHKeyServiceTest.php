<?php

namespace DigitalOceanDropletBundle\Tests\Integration\Service;

use DigitalOceanAccountBundle\Client\DigitalOceanClient;
use DigitalOceanAccountBundle\Service\DigitalOceanConfigService;
use DigitalOceanDropletBundle\Service\SSHKeyService;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class SSHKeyServiceTest extends TestCase
{
    public function testGetSSHKeyIds(): void
    {
        $client = $this->createMock(DigitalOceanClient::class);
        $configService = $this->createMock(DigitalOceanConfigService::class);
        $logger = $this->createMock(LoggerInterface::class);
        
        $configService->method('getConfig')->willReturn(null);
        
        $service = new SSHKeyService($client, $configService, $logger);
        
        $this->expectException(\DigitalOceanDropletBundle\Exception\DigitalOceanConfigurationException::class);
        $service->getSSHKeyIds();
    }
}