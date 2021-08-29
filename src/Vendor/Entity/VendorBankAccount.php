<?php

declare(strict_types=1);

namespace App\Vendor\Entity;

use App\Vendor\Repository\VendorBankAccountRepository;
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
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: VendorBankAccountRepository::class)]
#[Table(name: 'vendor_bank_account')]
class VendorBankAccount implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Column(type: 'uuid')]
    #[NotBlank]
    private ?UuidInterface $vendorId = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $bankCode = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $agencyNumber = null;

    #[Column(type: 'string', nullable: true)]
    #[NotBlank]
    private ?string $accountNumber = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $accountDigit = null;

    #[Column(type: 'string', nullable: true)]
    #[NotBlank]
    private ?string $ownerName = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $ownerDocumentType = null;

    #[Column(type: 'string', nullable: true)]
    private ?string $ownerDocumentNumber = null;

    #[Column(type: 'boolean', nullable: true)]
    private ?bool $isValid = null;

    public function getId(): UuidInterface
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

    public function getBankCode(): ?string
    {
        return $this->bankCode;
    }

    public function setBankCode(?string $bankCode): self
    {
        $this->bankCode = $bankCode;

        return $this;
    }

    public function getAgencyNumber(): ?string
    {
        return $this->agencyNumber;
    }

    public function setAgencyNumber(?string $agencyNumber): self
    {
        $this->agencyNumber = $agencyNumber;

        return $this;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber(?string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    public function getAccountDigit(): ?string
    {
        return $this->accountDigit;
    }

    public function setAccountDigit(?string $accountDigit): self
    {
        $this->accountDigit = $accountDigit;

        return $this;
    }

    public function getOwnerName(): ?string
    {
        return $this->ownerName;
    }

    public function setOwnerName(?string $ownerName): self
    {
        $this->ownerName = $ownerName;

        return $this;
    }

    public function getOwnerDocumentType(): ?string
    {
        return $this->ownerDocumentType;
    }

    public function setOwnerDocumentType(?string $ownerDocumentType): self
    {
        $this->ownerDocumentType = $ownerDocumentType;

        return $this;
    }

    public function getOwnerDocumentNumber(): ?string
    {
        return $this->ownerDocumentNumber;
    }

    public function setOwnerDocumentNumber(?string $ownerDocumentNumber): self
    {
        $this->ownerDocumentNumber = $ownerDocumentNumber;

        return $this;
    }

    public function getIsValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(?bool $isValid): self
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function isNew(): bool
    {
        return ! isset($this->id);
    }
}
