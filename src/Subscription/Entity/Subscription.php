<?php

namespace App\Subscription\Entity;

use App\Customer\Entity\Customer;
use App\Vendor\Entity\VendorPlan;
use Decimal\Decimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Subscription\Repository\SubscriptionRepository")
 * @ORM\Table(name="subscription")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Subscription
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Customer\Entity\Customer", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private Customer $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Vendor\Entity\VendorPlan", inversedBy="subscriptions")
     * @ORM\JoinColumn(name="vendor_plan_id", referencedColumnName="id")
     */
    private VendorPlan $vendorPlan;

    /**
     * @ORM\OneToMany(targetEntity="SubscriptionCycle", mappedBy="subscription", cascade={"persist"})
     */
    private Collection $cycles;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isApproved = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $reviewNotes = null;

    /**
     * @ORM\Column(name="reviewed_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $reviewedAt = null;

    /**
     * @ORM\Column(name="valid_from", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $validFrom = null;

    /**
     * @ORM\Column(name="expires_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $expiresAt = null;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $price;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isRecurring = true;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $isActive = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $cancelledByCustomer = null;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private ?bool $cancelledByVendor = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $cancellationNotes = null;

    /**
     * @ORM\Column(name="cancelled_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $cancelledAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $deletedAt = null;

    public function __construct()
    {
        $this->cycles = new ArrayCollection();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getVendorPlan(): VendorPlan
    {
        return $this->vendorPlan;
    }

    public function setVendorPlan(VendorPlan $vendorPlan): self
    {
        $this->vendorPlan = $vendorPlan;

        return $this;
    }

    public function isApproved(): ?bool
    {
        return $this->isApproved;
    }

    public function setIsApproved(?bool $isApproved): self
    {
        $this->isApproved = $isApproved;

        return $this;
    }

    public function getReviewNotes(): ?string
    {
        return $this->reviewNotes;
    }

    public function setReviewNotes(?string $reviewNotes): self
    {
        $this->reviewNotes = $reviewNotes;

        return $this;
    }

    public function getReviewedAt(): ?\DateTimeInterface
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?\DateTimeInterface $reviewedAt): self
    {
        $this->reviewedAt = $reviewedAt;

        return $this;
    }

    public function getValidFrom(): ?\DateTimeInterface
    {
        return $this->validFrom;
    }

    public function setValidFrom(?\DateTimeInterface $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getPrice(): Decimal
    {
        return new Decimal($this->price);
    }

    public function setPrice(Decimal $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): self
    {
        $this->isRecurring = $isRecurring;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCancelledByCustomer(): ?bool
    {
        return $this->cancelledByCustomer;
    }

    public function setCancelledByCustomer(?bool $cancelledByCustomer): self
    {
        $this->cancelledByCustomer = $cancelledByCustomer;

        return $this;
    }

    public function getCancelledByVendor(): ?bool
    {
        return $this->cancelledByVendor;
    }

    public function setCancelledByVendor(?bool $cancelledByVendor): self
    {
        $this->cancelledByVendor = $cancelledByVendor;

        return $this;
    }

    public function getCancellationNotes(): ?string
    {
        return $this->cancellationNotes;
    }

    public function setCancellationNotes(?string $cancellationNotes): self
    {
        $this->cancellationNotes = $cancellationNotes;

        return $this;
    }

    public function getCancelledAt(): ?\DateTimeInterface
    {
        return $this->cancelledAt;
    }

    public function setCancelledAt(?\DateTimeInterface $cancelledAt): self
    {
        $this->cancelledAt = $cancelledAt;

        return $this;
    }

    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeInterface $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }
}
