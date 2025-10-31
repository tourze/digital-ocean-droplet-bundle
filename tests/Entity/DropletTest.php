<?php

namespace DigitalOceanDropletBundle\Tests\Entity;

use DigitalOceanDropletBundle\Entity\Droplet;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Droplet::class)]
final class DropletTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Droplet();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'dropletId' => ['dropletId', 12345];
        yield 'name' => ['name', 'test-droplet'];
        yield 'memory' => ['memory', '2048'];
        yield 'vcpus' => ['vcpus', '2'];
        yield 'disk' => ['disk', '40'];
        yield 'region' => ['region', 'sgp1'];
        yield 'imageId' => ['imageId', '123456'];
        yield 'imageName' => ['imageName', 'Ubuntu 20.04'];
        yield 'status' => ['status', 'active'];
        yield 'networks' => ['networks', ['v4' => [['ip_address' => '192.168.1.1']]]];
        yield 'tags' => ['tags', ['web', 'production']];
        yield 'volumeIds' => ['volumeIds', ['vol-123', 'vol-456']];
    }

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
                    'type' => 'public',
                ],
            ],
        ];
        $droplet->setNetworks($networks);

        $tags = ['web', 'production'];
        $droplet->setTags($tags);

        $volumeIds = ['vol-123', 'vol-456'];
        $droplet->setVolumeIds($volumeIds);

        return $droplet;
    }

    public function testNullableFields(): void
    {
        $droplet = new Droplet();

        $this->assertNull($droplet->getImageName());
        $this->assertNull($droplet->getNetworks());
        $this->assertNull($droplet->getTags());
        $this->assertNull($droplet->getVolumeIds());
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
        $this->assertArrayHasKey('dropletId', $adminArray);
        $this->assertArrayHasKey('name', $adminArray);
        $this->assertArrayHasKey('status', $adminArray);

        // 验证值是否正确
        $this->assertEquals(12345, $adminArray['dropletId']);
        $this->assertEquals('test-droplet', $adminArray['name']);
        $this->assertEquals('active', $adminArray['status']);
    }

    public function testSetterMethods(): void
    {
        $droplet = new Droplet();

        // 测试setter方法（现在返回void）
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

        $networks = ['v4' => [['ip_address' => '192.168.1.1']]];
        $droplet->setNetworks($networks);
        $this->assertEquals($networks, $droplet->getNetworks());

        $tags = ['web', 'production'];
        $droplet->setTags($tags);
        $this->assertEquals($tags, $droplet->getTags());

        $volumeIds = ['vol-123', 'vol-456'];
        $droplet->setVolumeIds($volumeIds);
        $this->assertEquals($volumeIds, $droplet->getVolumeIds());

        $createTime = new \DateTimeImmutable();
        $droplet->setCreateTime($createTime);
        $this->assertEquals($createTime, $droplet->getCreateTime());
    }
}
