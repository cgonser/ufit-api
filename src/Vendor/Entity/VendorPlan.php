<?php

declare(strict_types=1);

namespace App\Vendor\Entity;

use App\Localization\Entity\Currency;
use App\Payment\Entity\PaymentMethod;
use App\Subscription\Entity\Subscription;
use App\Vendor\Repository\VendorPlanRepository;
use DateInterval;
use Decimal\Decimal;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\CustomIdGenerator;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: VendorPlanRepository::class)]
#[Table(name: 'vendor_plan')]
class VendorPlan implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[Id]
    #[Column(type: 'uuid', unique: true)]
    #[GeneratedValue(strategy: 'CUSTOM')]
    #[CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[Column(type: 'uuid')]
    private ?UuidInterface $vendorId = null;

    #[ManyToOne(targetEntity: 'Vendor')]
    #[JoinColumn(name: 'vendor_id', nullable: false)]
    private Vendor $vendor;

    #[ManyToOne(targetEntity: 'Questionnaire')]
    #[JoinColumn(name: 'questionnaire_id')]
    private ?Questionnaire $questionnaire = null;

    #[Column(type: 'string')]
    #[NotBlank]
    private ?string $name = null;

    #[Column(type: 'string', nullable: true)]
    #[NotBlank]
    private ?string $slug = null;

    #[Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[Column(type: 'json', nullable: true)]
    private ?array $features = null;

    #[Column(type: 'decimal', options: ['precision' => 11, 'scale' => 2,])]
    #[NotBlank]
    private Decimal|string|null $price;

    #[Column(type: 'string', nullable: true)]
    private ?string $image = null;

    #[ManyToMany(targetEntity: PaymentMethod::class)]
    private Collection $paymentMethods;

    /**
     * @var Subscription[]|Collection<int, Subscription>
     */
    #[OneToMany(mappedBy: 'vendorPlan', targetEntity: Subscription::class)]
    private Collection $subscriptions;

    #[ManyToOne(targetEntity: Currency::class)]
    #[JoinColumn(name: 'currency_id')]
    #[NotBlank]
    private Currency $currency;

    #[Column(type: 'dateinterval', nullable: true)]
    private ?DateInterval $duration = null;

    #[Column(type: 'boolean', options: [
        'default' => false,
    ])]
    private bool $isApprovalRequired = false;

    #[Column(type: 'boolean', options: [
        'default' => true,
    ])]
    private bool $isVisible = true;

    #[Column(type: 'boolean', options: [
        'default' => true,
    ])]
    private bool $isActive = true;

    #[Column(type: 'boolean', options: [
        'default' => true,
    ])]
    private bool $isRecurring = true;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->paymentMethods = new ArrayCollection();
    }

    public function getId(): UuidInterface
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
        $this->vendorId = $vendor->getId();
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
        if (is_array($features) && [] === $features) {
            $features = null;
        }

        $this->features = $features;

        return $this;
    }

    public function getPrice(): Decimal
    {
        return new Decimal($this->price);
    }

    public function setPrice(Decimal|string $price): self
    {
        $this->price = is_string($price) ? new Decimal($price) : $price;

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

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function addSubscription(Subscription $subscription): self
    {
        $this->subscriptions->add($subscription);

        return $this;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function setDuration(DateInterval $dateInterval): self
    {
        $this->duration = $dateInterval;

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
        return ! isset($this->id);
    }
}
