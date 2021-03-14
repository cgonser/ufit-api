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
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorBankAccountRepository")
 * @ORM\Table(name="vendor_bank_account")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class VendorBankAccount
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
     * @ORM\ManyToOne(targetEntity="Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", nullable=true)
     */
    private ?Vendor $vendor = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $bankCode = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $agencyNumber = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $accountNumber = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $accountDigit = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ownerName = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ownerDocumentType = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ownerDocumentNumber = null;

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

    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    public function setVendor(Vendor $vendor): self
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): VendorBankAccount
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getAgencyNumber(): ?string
    {
        return $this->agencyNumber;
    }

    public function setAgencyNumber(?string $agencyNumber): VendorBankAccount
    {
        $this->agencyNumber = $agencyNumber;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): VendorBankAccount
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountDigit(): ?string
    {
        return $this->accountDigit;
    }

    public function setAccountDigit(?string $accountDigit): VendorBankAccount
    {
        $this->accountDigit = $accountDigit;

        return $this;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(?string $ownerName): VendorBankAccount
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getOwnerDocumentType(): ?string
    {
        return $this->ownerDocumentType;
    }

    public function setOwnerDocumentType(?string $ownerDocumentType): VendorBankAccount
    {
        $this->ownerDocumentType = $ownerDocumentType;

        return $this;
    }

    public function getOwnerDocumentNumber(): ?string
    {
        return $this->ownerDocumentNumber;
    }

    public function setOwnerDocumentNumber(?string $ownerDocumentNumber): VendorBankAccount
    {
        $this->ownerDocumentNumber = $ownerDocumentNumber;

        return $this;
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
