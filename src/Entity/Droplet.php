<?php

namespace DigitalOceanDropletBundle\Entity;

use DigitalOceanDropletBundle\Repository\DropletRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

/**
 * @implements PlainArrayInterface<string, mixed>
 * @implements AdminArrayInterface<string, mixed>
 */
#[ORM\Entity(repositoryClass: DropletRepository::class)]
#[ORM\Table(name: 'ims_digital_ocean_droplet', options: ['comment' => 'DigitalOcean虚拟机'])]
class Droplet implements PlainArrayInterface, AdminArrayInterface, \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '虚拟机ID'])]
    #[IndexColumn]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private int $dropletId;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '虚拟机名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '内存大小(MB)'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $memory;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => 'CPU核心数'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $vcpus;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '磁盘大小(GB)'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $disk;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '区域'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $region;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '镜像ID'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $imageId;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '镜像名称'])]
    #[Assert\Length(max: 255)]
    private ?string $imageName = null;

    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '状态'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private string $status;

    /**
     * @var array<string, mixed>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '网络信息'])]
    #[Assert\Type(type: 'array')]
    private ?array $networks = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '标签'])]
    #[Assert\Type(type: 'array')]
    private ?array $tags = null;

    /**
     * @var array<string>|null
     */
    #[ORM\Column(type: Types::JSON, nullable: true, options: ['comment' => '卷IDs'])]
    #[Assert\Type(type: 'array')]
    private ?array $volumeIds = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getDropletId(): int
    {
        return $this->dropletId;
    }

    public function setDropletId(int $dropletId): void
    {
        $this->dropletId = $dropletId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMemory(): string
    {
        return $this->memory;
    }

    public function setMemory(string $memory): void
    {
        $this->memory = $memory;
    }

    public function getVcpus(): string
    {
        return $this->vcpus;
    }

    public function setVcpus(string $vcpus): void
    {
        $this->vcpus = $vcpus;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }

    public function setDisk(string $disk): void
    {
        $this->disk = $disk;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function setRegion(string $region): void
    {
        $this->region = $region;
    }

    public function getImageId(): string
    {
        return $this->imageId;
    }

    public function setImageId(string $imageId): void
    {
        $this->imageId = $imageId;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getNetworks(): ?array
    {
        return $this->networks;
    }

    /**
     * @param array<string, mixed>|null $networks
     */
    public function setNetworks(?array $networks): void
    {
        $this->networks = $networks;
    }

    /**
     * @return array<string>|null
     */
    public function getTags(): ?array
    {
        return $this->tags;
    }

    /**
     * @param array<string>|null $tags
     */
    public function setTags(?array $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @return array<string>|null
     */
    public function getVolumeIds(): ?array
    {
        return $this->volumeIds;
    }

    /**
     * @param array<string>|null $volumeIds
     */
    public function setVolumeIds(?array $volumeIds): void
    {
        $this->volumeIds = $volumeIds;
    }

    /**
     * @return array<string, mixed>
     */
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
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(): array
    {
        return $this->toPlainArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrievePlainArray(): array
    {
        return $this->toPlainArray();
    }

    /**
     * @return array<string, mixed>
     */
    public function retrieveAdminArray(): array
    {
        return $this->toAdminArray();
    }
}
