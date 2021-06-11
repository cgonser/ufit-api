<?php

namespace App\Vendor\Entity;

use App\Localization\Entity\Currency;
use App\Payment\Entity\PaymentMethod;
use App\Subscription\Entity\Subscription;
use Decimal\Decimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Vendor\Repository\VendorPlanRepository")
 * @ORM\Table(name="vendor_plan")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class VendorPlan
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
     * @ORM\Column(type="uuid")
     */
    private ?UuidInterface $vendorId = null;

    /**
     * @ORM\ManyToOne(targetEntity="Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id", nullable=false)
     */
    private Vendor $vendor;

    /**
     * @ORM\ManyToOne(targetEntity="Questionnaire")
     * @ORM\JoinColumn(name="questionnaire_id", referencedColumnName="id", nullable=true)
     */
    private ?Questionnaire $questionnaire = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private ?array $features = null;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     * @Assert\NotBlank()
     */
    private string $price;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $image = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Payment\Entity\PaymentMethod")
     */
    private Collection $paymentMethods;

    /**
     * @ORM\OneToMany(targetEntity="App\Subscription\Entity\Subscription", mappedBy="vendorPlan")
     */
    private Collection $subscriptions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Localization\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     * @Assert\NotBlank()
     */
    private Currency $currency;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private ?\DateInterval $duration = null;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": false})
     */
    private bool $isApprovalRequired = false;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isVisible = true;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isActive = true;

    /**
     * @ORM\Column(type="boolean", nullable=false, options={"default": true})
     */
    private bool $isRecurring = true;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
    }

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getVendorId(): ?UuidInterface
    {
        return $this->vendorId;
    }

    public function setVendorId(?UuidInterface $vendorId): self
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

    public function getQuestionnaire(): ?Questionnaire
    {
        return $this->questionnaire;
    }

    public function setQuestionnaire(?Questionnaire $questionnaire = null): self
    {
        $this->questionnaire = $questionnaire;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getFeatures(): ?array
    {
        return $this->features;
    }

    public function setFeatures(?array $features): self
    {
        if (is_array($features) && 0 == count($features)) {
            $features = null;
        }

        $this->features = $features;

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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getPaymentMethods(): Collection
    {
        return $this->paymentMethods;
    }

    public function addPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethods->add($paymentMethod);

        return $this;
    }

    public function getSubscriptions()
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        $this->subscriptions->add($subscription);

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

    public function isApprovalRequired(): bool
    {
        return $this->isApprovalRequired;
    }

    public function setIsApprovalRequired(bool $isApprovalRequired): self
    {
        $this->isApprovalRequired = $isApprovalRequired;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;

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

    public function isRecurring(): bool
    {
        return $this->isRecurring;
    }

    public function setIsRecurring(bool $isRecurring): self
    {
        $this->isRecurring = $isRecurring;

        return $this;
    }

    public function isNew(): bool
    {
        return !isset($this->id);
    }
}
