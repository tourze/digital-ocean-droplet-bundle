<?php

namespace DigitalOceanDropletBundle\DataFixtures;

use DigitalOceanDropletBundle\Entity\Droplet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class DropletFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $droplet1 = new Droplet();
        $droplet1->setDropletId(123456789);
        $droplet1->setName('web-server-001');
        $droplet1->setMemory('1024');
        $droplet1->setVcpus('1');
        $droplet1->setDisk('25');
        $droplet1->setRegion('nyc1');
        $droplet1->setImageId('ubuntu-20-04-x64');
        $droplet1->setImageName('Ubuntu 20.04 x64');
        $droplet1->setStatus('active');
        $droplet1->setNetworks([
            'v4' => [
                [
                    'ip_address' => '192.168.1.100',
                    'type' => 'private',
                ],
                [
                    'ip_address' => '203.0.113.10',
                    'type' => 'public',
                ],
            ],
        ]);
        $droplet1->setTags(['web', 'production']);
        $droplet1->setVolumeIds(['vol-1', 'vol-2']);

        $droplet2 = new Droplet();
        $droplet2->setDropletId(987654321);
        $droplet2->setName('db-server-001');
        $droplet2->setMemory('2048');
        $droplet2->setVcpus('2');
        $droplet2->setDisk('50');
        $droplet2->setRegion('sgp1');
        $droplet2->setImageId('ubuntu-20-04-x64');
        $droplet2->setImageName('Ubuntu 20.04 x64');
        $droplet2->setStatus('active');
        $droplet2->setNetworks([
            'v4' => [
                [
                    'ip_address' => '192.168.1.101',
                    'type' => 'private',
                ],
            ],
        ]);
        $droplet2->setTags(['database', 'production']);
        $droplet2->setVolumeIds(['vol-3']);

        $droplet3 = new Droplet();
        $droplet3->setDropletId(555666777);
        $droplet3->setName('test-server-001');
        $droplet3->setMemory('512');
        $droplet3->setVcpus('1');
        $droplet3->setDisk('20');
        $droplet3->setRegion('fra1');
        $droplet3->setImageId('centos-8-x64');
        $droplet3->setImageName('CentOS 8 x64');
        $droplet3->setStatus('off');
        $droplet3->setNetworks([
            'v4' => [
                [
                    'ip_address' => '203.0.113.20',
                    'type' => 'public',
                ],
            ],
        ]);
        $droplet3->setTags(['testing']);

        $manager->persist($droplet1);
        $manager->persist($droplet2);
        $manager->persist($droplet3);
        $manager->flush();
    }
}
