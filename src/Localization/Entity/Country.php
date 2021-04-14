<?php

namespace App\Localization\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Localization\Repository\CountryRepository")
 * @ORM\Table(name="country")
 * @UniqueEntity(fields={"code"})
 */
class Country
{
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
    private ?UuidInterface $currencyId = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Localization\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private ?Currency $currency = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $primaryTimezone = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $timezones = [];

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank()
     */
    private string $code;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $primaryLocale = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $vendorsEnabled = false;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private bool $customersEnabled = true;

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

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCurrencyId(): UuidInterface
    {
        return $this->currencyId;
    }

    public function setCurrencyId(UuidInterface $currencyId): self
    {
        $this->currencyId = $currencyId;

        return $this;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getPrimaryTimezone(): ?string
    {
        return $this->primaryTimezone;
    }

    public function setPrimaryTimezone(?string $primaryTimezone): self
    {
        $this->primaryTimezone = $primaryTimezone;

        return $this;
    }

    public function getTimezones(): array
    {
        return $this->timezones;
    }

    public function setTimezones(array $timezones): self
    {
        $this->timezones = $timezones;

        return $this;
    }

    public function getPrimaryLocale(): ?string
    {
        return $this->primaryLocale;
    }

    public function setPrimaryLocale(string $primaryLocale): self
    {
        $this->primaryLocale = $primaryLocale;

        return $this;
    }

    public function isCustomersEnabled(): bool
    {
        return $this->customersEnabled;
    }

    public function setCustomersEnabled(bool $customersEnabled): self
    {
        $this->customersEnabled = $customersEnabled;

        return $this;
    }

    public function isVendorsEnabled(): bool
    {
        return $this->vendorsEnabled;
    }

    public function setVendorsEnabled(bool $vendorsEnabled): self
    {
        $this->vendorsEnabled = $vendorsEnabled;

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
