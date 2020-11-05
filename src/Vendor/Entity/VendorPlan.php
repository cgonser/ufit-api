<?php

namespace App\Vendor\Entity;

use App\Core\Entity\Currency;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorPlanRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="vendor_plan")
 */
class VendorPlan
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */

    private Vendor $vendor;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private string $name;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private int $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private Currency $currency;

    /**
     * @ORM\Column(type="dateinterval")
     */
    private \DateInterval $duration;

    /**
     * @ORM\Column(name="created_at", type="datetimetz", nullable=true)
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetimetz", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        return $this->duration;
    }

    public function setDuration(\DateInterval $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt = null): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt = null): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTime());
        }

        if (!$this->getUpdatedAt()) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdatedAt(new \DateTime());
    }
}
