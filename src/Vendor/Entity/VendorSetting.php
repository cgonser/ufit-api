<?php

declare(strict_types=1);

namespace App\Vendor\Entity;

use App\Vendor\Repository\VendorSettingRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[Entity(repositoryClass: VendorSettingRepository::class)]
#[Table(name: 'vendor_settings')]
class VendorSetting implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private ?UuidInterface $id = null;

    #[Column(type: 'uuid', nullable: true)]
    private ?UuidInterface $vendorId = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $name = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $value = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getVendorId(): ?UuidInterface
    {
        return $this->vendorId;
    }

    public function setVendorId(?UuidInterface $vendorId): void
    {
        $this->vendorId = $vendorId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function isNew(): bool
    {
        return ! isset($this->id);
    }
}
