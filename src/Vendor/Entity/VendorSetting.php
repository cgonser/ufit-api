<?php

namespace App\Vendor\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorSettingRepository")
 * @ORM\Table(name="vendor_settings")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class VendorSetting
{
    use TimestampableEntity;
    use SoftDeleteableEntity;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="uuid", nullable=true)
     */
    private ?UuidInterface $vendorId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
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

    public function setName(?string $name): VendorSetting
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): VendorSetting
    {
        $this->value = $value;

        return $this;
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
