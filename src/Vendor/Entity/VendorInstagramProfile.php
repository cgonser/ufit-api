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
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorInstagramProfileRepository")
 * @ORM\Table(name="vendor_instagram_profile")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class VendorInstagramProfile
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
     * @ORM\ManyToOne(targetEntity="Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private ?Vendor $vendor = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $instagramId = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $accessToken = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $code = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Assert\NotBlank()
     */
    private ?bool $isBusiness = null;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getInstagramId(): ?string
    {
        return $this->instagramId;
    }

    public function setInstagramId(string $instagramId): self
    {
        $this->instagramId = $instagramId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function isBusiness(): ?bool
    {
        return $this->isBusiness;
    }

    public function setIsBusiness(bool $isBusiness): self
    {
        $this->isBusiness = $isBusiness;

        return $this;
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
