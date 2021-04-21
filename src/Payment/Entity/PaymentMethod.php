<?php

namespace App\Payment\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Payment\Repository\PaymentMethodRepository")
 * @ORM\Table(name="payment_method")
 */
class PaymentMethod
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     */
    private ?array $countriesEnabled = null;

    /**
     * @ORM\Column(type="jsonb", nullable=true)
     */
    private ?array $countriesDisabled = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isActive = true;

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

    public function __toString(): string
    {
        return $this->getName();
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
