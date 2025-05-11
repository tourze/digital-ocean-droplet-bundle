<?php

namespace DigitalOceanDropletBundle\Tests\Entity;

use DigitalOceanDropletBundle\Entity\Droplet;
use PHPUnit\Framework\TestCase;

class DropletTest extends TestCase
{
    private function createCompleteDroplet(): Droplet
    {
        $droplet = new Droplet();
        $droplet->setDropletId(12345);
        $droplet->setName('test-droplet');
        $droplet->setMemory('2048');
        $droplet->setVcpus('2');
        $droplet->setDisk('40');
        $droplet->setRegion('sgp1');
        $droplet->setImageId('123456');
        $droplet->setImageName('Ubuntu 20.04');
        $droplet->setStatus('active');
        
        $networks = [
            'v4' => [
                [
                    'ip_address' => '192.168.1.1',
                    'netmask' => '255.255.255.0',
                    'gateway' => '192.168.1.254',
                    'type' => 'public'
                ]
            ]
        ];
        $droplet->setNetworks($networks);
        
        $tags = ['web', 'production'];
        $droplet->setTags($tags);
        
        $volumeIds = ['vol-123', 'vol-456'];
        $droplet->setVolumeIds($volumeIds);
        
        return $droplet;
    }

    public function testGettersAndSetters(): void
    {
        $droplet = new Droplet();
        
        // 基本属性测试
        $droplet->setDropletId(12345);
        $this->assertEquals(12345, $droplet->getDropletId());
        
        $droplet->setName('test-droplet');
        $this->assertEquals('test-droplet', $droplet->getName());
        
        $droplet->setMemory('2048');
        $this->assertEquals('2048', $droplet->getMemory());
        
        $droplet->setVcpus('2');
        $this->assertEquals('2', $droplet->getVcpus());
        
        $droplet->setDisk('40');
        $this->assertEquals('40', $droplet->getDisk());
        
        $droplet->setRegion('sgp1');
        $this->assertEquals('sgp1', $droplet->getRegion());
        
        $droplet->setImageId('123456');
        $this->assertEquals('123456', $droplet->getImageId());
        
        $droplet->setImageName('Ubuntu 20.04');
        $this->assertEquals('Ubuntu 20.04', $droplet->getImageName());
        
        $droplet->setStatus('active');
        $this->assertEquals('active', $droplet->getStatus());
        
        // 复杂属性测试
        $networks = [
            'v4' => [
                [
                    'ip_address' => '192.168.1.1',
                    'netmask' => '255.255.255.0',
                    'gateway' => '192.168.1.254',
                    'type' => 'public'
                ]
            ]
        ];
        $droplet->setNetworks($networks);
        $this->assertEquals($networks, $droplet->getNetworks());
        
        $tags = ['web', 'production'];
        $droplet->setTags($tags);
        $this->assertEquals($tags, $droplet->getTags());
        
        $volumeIds = ['vol-123', 'vol-456'];
        $droplet->setVolumeIds($volumeIds);
        $this->assertEquals($volumeIds, $droplet->getVolumeIds());
        
        // 日期时间测试
        $now = new \DateTimeImmutable();
        $droplet->setCreatedAt($now);
        $this->assertSame($now, $droplet->getCreatedAt());
        
        $updateTime = new \DateTime();
        $droplet->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $droplet->getUpdateTime());
        
        $createTime = new \DateTime();
        $droplet->setCreateTime($createTime);
        $this->assertSame($createTime, $droplet->getCreateTime());
    }
    
    public function testNullableFields(): void
    {
        $droplet = new Droplet();
        
        $this->assertNull($droplet->getImageName());
        $this->assertNull($droplet->getNetworks());
        $this->assertNull($droplet->getTags());
        $this->assertNull($droplet->getVolumeIds());
        $this->assertNull($droplet->getCreatedAt());
        $this->assertNull($droplet->getCreateTime());
        $this->assertNull($droplet->getUpdateTime());
        
        // 测试设置为 null
        $droplet->setImageName('test');
        $this->assertEquals('test', $droplet->getImageName());
        $droplet->setImageName(null);
        $this->assertNull($droplet->getImageName());
    }
    
    public function testToAdminArray(): void
    {
        $droplet = $this->createCompleteDroplet();
        
        $adminArray = $droplet->toAdminArray();
        
        // 验证 toAdminArray 返回的数组包含所有必要的键
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('dropletId', $adminArray);
        $this->assertArrayHasKey('name', $adminArray);
        $this->assertArrayHasKey('status', $adminArray);
        
        // 验证值是否正确
        $this->assertEquals(12345, $adminArray['dropletId']);
        $this->assertEquals('test-droplet', $adminArray['name']);
        $this->assertEquals('active', $adminArray['status']);
    }
    
    public function testRetrievePlainArray(): void
    {
        $droplet = $this->createCompleteDroplet();
        
        $plainArray = $droplet->retrievePlainArray();
        
        // 验证 retrievePlainArray 返回的数组包含所有必要的键
        $this->assertIsArray($plainArray);
        $this->assertArrayHasKey('dropletId', $plainArray);
        $this->assertArrayHasKey('name', $plainArray);
        $this->assertArrayHasKey('status', $plainArray);
        
        // 验证值是否正确
        $this->assertEquals(12345, $plainArray['dropletId']);
        $this->assertEquals('test-droplet', $plainArray['name']);
        $this->assertEquals('active', $plainArray['status']);
    }
    
    public function testRetrieveAdminArray(): void
    {
        $droplet = $this->createCompleteDroplet();
        
        $adminArray = $droplet->retrieveAdminArray();
        
        // 验证 retrieveAdminArray 返回的数组包含所有必要的键
        $this->assertIsArray($adminArray);
        $this->assertArrayHasKey('dropletId', $adminArray);
        $this->assertArrayHasKey('name', $adminArray);
        $this->assertArrayHasKey('status', $adminArray);
        
        // 验证值是否正确
        $this->assertEquals(12345, $adminArray['dropletId']);
        $this->assertEquals('test-droplet', $adminArray['name']);
        $this->assertEquals('active', $adminArray['status']);
    }
    
    public function testFluidInterface(): void
    {
        $droplet = new Droplet();
        
        // 测试流式接口
        $this->assertInstanceOf(Droplet::class, $droplet->setDropletId(12345));
        $this->assertInstanceOf(Droplet::class, $droplet->setName('test-droplet'));
        $this->assertInstanceOf(Droplet::class, $droplet->setMemory('2048'));
        $this->assertInstanceOf(Droplet::class, $droplet->setVcpus('2'));
        $this->assertInstanceOf(Droplet::class, $droplet->setDisk('40'));
        $this->assertInstanceOf(Droplet::class, $droplet->setRegion('sgp1'));
        $this->assertInstanceOf(Droplet::class, $droplet->setImageId('123456'));
        $this->assertInstanceOf(Droplet::class, $droplet->setImageName('Ubuntu 20.04'));
        $this->assertInstanceOf(Droplet::class, $droplet->setStatus('active'));
        $this->assertInstanceOf(Droplet::class, $droplet->setNetworks([]));
        $this->assertInstanceOf(Droplet::class, $droplet->setTags([]));
        $this->assertInstanceOf(Droplet::class, $droplet->setVolumeIds([]));
        $this->assertInstanceOf(Droplet::class, $droplet->setCreatedAt(new \DateTimeImmutable()));
    }
} 