<?php

namespace DigitalOceanDropletBundle\Entity;

use DigitalOceanDropletBundle\Repository\DropletRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;

#[ORM\Entity(repositoryClass: DropletRepository::class)]
#[ORM\Table(name: 'ims_digital_ocean_droplet', options: ['comment' => 'DigitalOcean虚拟机'])]
class Droplet implements PlainArrayInterface, AdminArrayInterface
{
    use TimestampableAware;
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '虚拟机ID'])]
    #[IndexColumn]
    private int $dropletId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '虚拟机名称'])]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '内存大小(MB)'])]
    private string $memory;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'CPU核心数'])]
    private string $vcpus;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '磁盘大小(GB)'])]
    private string $disk;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '区域'])]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '镜像ID'])]
    private string $imageId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '镜像名称'])]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '状态'])]
    private string $status;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '网络信息'])]
    private ?array $networks = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    private ?array $tags = null;

    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '卷IDs'])]
    private ?array $volumeIds = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDropletId(): int
    {
        return $this->dropletId;
    }

    public function setDropletId(int $dropletId): self
    {
        $this->dropletId = $dropletId;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getMemory(): string
    {
        return $this->memory;
    }

    public function setMemory(string $memory): self
    {
        $this->memory = $memory;
        return $this;
    }

    public function getVcpus(): string
    {
        return $this->vcpus;
    }

    public function setVcpus(string $vcpus): self
    {
        $this->vcpus = $vcpus;
        return $this;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function setDisk(string $disk): self
    {
        $this->disk = $disk;
        return $this;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }

    public function setImageId(string $imageId): self
    {
        $this->imageId = $imageId;
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getNetworks(): ?array
    {
        return $this->networks;
    }

    public function setNetworks(?array $networks): self
    {
        $this->networks = $networks;
        return $this;
    }

    public function getTags(): ?array
    {
        return $this->tags;
    }

    public function setTags(?array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getVolumeIds(): ?array
    {
        return $this->volumeIds;
    }

    public function setVolumeIds(?array $volumeIds): self
    {
        $this->volumeIds = $volumeIds;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function toPlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'dropletId' => $this->getDropletId(),
            'name' => $this->getName(),
            'memory' => $this->getMemory(),
            'vcpus' => $this->getVcpus(),
            'disk' => $this->getDisk(),
            'region' => $this->getRegion(),
            'imageId' => $this->getImageId(),
            'imageName' => $this->getImageName(),
            'status' => $this->getStatus(),
            'networks' => $this->getNetworks(),
            'tags' => $this->getTags(),
            'volumeIds' => $this->getVolumeIds(),
            'createdAt' => $this->getCreatedAt()?->format('Y-m-d H:i:s'),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function toAdminArray(): array
    {
        return $this->toPlainArray();
    }

    public function retrievePlainArray(): array
    {
        return $this->toPlainArray();
    }

    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }}
