<?php

declare(strict_types=1);

namespace App\Payment\Entity;

use Doctrine\ORM\Mapping\Entity;
use App\Payment\Repository\PaymentMethodRepository;
use Doctrine\ORM\Mapping\Table;
use Stringable;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Mapping;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints;

#[Entity(repositoryClass: PaymentMethodRepository::class)]
#[Table(name: 'payment_method')]
class PaymentMethod implements Stringable
{
    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;
    #[Column(type: 'string')]
    #[NotBlank]
    private string $name;
    #[Column(type: 'jsonb', nullable: true)]
    private ?array $countriesEnabled = null;
    #[Column(type: 'jsonb', nullable: true)]
    private ?array $countriesDisabled = null;
    #[Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;
    public function __toString(): string
    {
        return $this->getName();
    }
    public function getId(): UuidInterface
    {
        return $this->id;
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
    public function getCountriesEnabled(): ?array
    {
        return $this->countriesEnabled;
    }
    public function setCountriesEnabled(?array $countriesEnabled): self
    {
        $this->countriesEnabled = $countriesEnabled;

        return $this;
    }
    public function getCountriesDisabled(): ?array
    {
        return $this->countriesDisabled;
    }
    public function setCountriesDisabled(?array $countriesDisabled): self
    {
        $this->countriesDisabled = $countriesDisabled;

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
