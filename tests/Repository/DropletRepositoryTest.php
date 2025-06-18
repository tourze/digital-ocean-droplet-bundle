<?php

namespace DigitalOceanDropletBundle\Tests\Repository;

use DigitalOceanDropletBundle\Repository\DropletRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class DropletRepositoryTest extends TestCase
{
    private ManagerRegistry $registry;
    private DropletRepository $repository;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->repository = new DropletRepository($this->registry);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(DropletRepository::class, $this->repository);
    }
    
    public function testClassType(): void
    {
        // 验证 Repository 是否继承自 ServiceEntityRepository
        $this->assertInstanceOf(ServiceEntityRepository::class, $this->repository);
    }
} 