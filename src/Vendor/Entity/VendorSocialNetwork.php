<?php

declare(strict_types=1);

namespace App\Vendor\Entity;

use App\Vendor\Repository\VendorSocialNetworkRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[Entity(repositoryClass: VendorSocialNetworkRepository::class)]
#[Table]
class VendorSocialNetwork implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    /**
     * @var string
     */
    public const PLATFORM_FACEBOOK = 'facebook';

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]

    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Column(type: 'uuid')]
    private UuidInterface $vendorId;

    #[ManyToOne(targetEntity: 'Vendor')]
    #[JoinColumn(name: 'vendor_id', nullable: false)]
    private Vendor $vendor;

    #[Column(type: 'string')]
    private string $platform;

    #[Column(type: 'string')]
    private string $externalId;

    #[Column(type: 'text')]
    private string $accessToken;

    #[Column(type: 'json')]
    private ?array $details = null;

    #[Column(type: 'boolean', options: [
        'default' => true,
    ])]
    private bool $isActive = true;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getVendorId(): UuidInterface
    {
        return $this->vendorId;
    }

    public function setVendorId(UuidInterface $vendorId): self
    {
        $this->vendorId = $vendorId;

        return $this;
    }

    public function getVendor(): Vendor
    {
        return $this->vendor;
    }

    public function setVendor(Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function setPlatform(string $platform): self
    {
        $this->platform = $platform;

        return $this;
    }

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function isNew(): bool
    {
        return ! isset($this->id);
    }
}
