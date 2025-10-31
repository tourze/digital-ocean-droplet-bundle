<?php

namespace DigitalOceanDropletBundle\Tests\Repository;

use DigitalOceanDropletBundle\Entity\Droplet;
use DigitalOceanDropletBundle\Repository\DropletRepository;
use Doctrine\ORM\Persisters\Exception\UnrecognizedField;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(DropletRepository::class)]
#[RunTestsInSeparateProcesses]
final class DropletRepositoryTest extends AbstractRepositoryTestCase
{
    private DropletRepository $repository;

    protected function createNewEntity(): object
    {
        return $this->createValidDroplet('test-droplet-' . uniqid(), 12345 + random_int(1, 10000));
    }

    protected function getRepository(): DropletRepository
    {
        if (!isset($this->repository)) {
            $this->repository = self::getService(DropletRepository::class);
        }

        return $this->repository;
    }

    protected function onSetUp(): void
    {
        // 自定义初始化逻辑
    }

    public function testCountWhenNoRecordsExistShouldReturnZero(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $existingEntities = $repository->findAll();
        foreach ($existingEntities as $entity) {
            $repository->remove($entity);
        }

        $count = $repository->count([]);
        $this->assertSame(0, $count);
    }

    public function testCountWhenRecordsExistShouldReturnCorrectNumber(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $existingEntities = $repository->findAll();
        foreach ($existingEntities as $entity) {
            $repository->remove($entity);
        }

        $entity = $this->createValidDroplet('test-droplet', 12345);
        $repository->save($entity);

        $count = $repository->count([]);
        $this->assertSame(1, $count);
    }

    public function testFindOneByWithNullCriteriaShouldReturnValidResult(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $existingEntities = $repository->findAll();
        foreach ($existingEntities as $entity) {
            $repository->remove($entity);
        }
        $entity = $this->createValidDroplet('test-droplet', 12345);
        $repository->save($entity);

        $found = $repository->findOneBy(['name' => null]);
        $this->assertNull($found);
    }

    public function testCountWithNullCriteriaShouldReturnValidResult(): void
    {
        $repository = $this->getRepository();
        $count = $repository->count(['name' => null]);
        $this->assertSame(0, $count);
    }

    public function testSaveEntityShouldPersistToDatabase(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $existingEntities = $repository->findAll();
        foreach ($existingEntities as $entity) {
            $repository->remove($entity);
        }
        $entity = $this->createValidDroplet('test-droplet', 12345);

        $repository->save($entity);

        $found = $repository->find($entity->getId());
        $this->assertNotNull($found);
        $this->assertSame('test-droplet', $found->getName());
    }

    public function testRemoveEntityShouldDeleteFromDatabase(): void
    {
        $repository = $this->getRepository();

        // 清理现有数据
        $existingEntities = $repository->findAll();
        foreach ($existingEntities as $entity) {
            $repository->remove($entity);
        }
        $entity = $this->createValidDroplet('test-droplet', 12345);
        $repository->save($entity);

        $id = $entity->getId();
        $repository->remove($entity);

        $found = $repository->find($id);
        $this->assertNull($found);
    }

    public function testFindByWithInvalidFieldQuery(): void
    {
        $this->expectException(UnrecognizedField::class);

        $repository = $this->getRepository();
        $repository->findBy(['invalidField' => 'value']);
    }

    private function createValidDroplet(string $name, int $dropletId): Droplet
    {
        $entity = new Droplet();
        $entity->setDropletId($dropletId);
        $entity->setName($name);
        $entity->setMemory('1024');
        $entity->setVcpus('1');
        $entity->setDisk('25');
        $entity->setRegion('nyc3');
        $entity->setImageId('ubuntu-20-04-x64');
        $entity->setStatus('active');

        return $entity;
    }

    public function testCountWithNonExistentIdShouldReturnZero(): void
    {
        $repository = $this->getRepository();
        $count = $repository->count(['id' => -88888]);
        $this->assertSame(0, $count);
    }
}
