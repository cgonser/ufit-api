<?php

declare(strict_types=1);

namespace App\Payment\Entity;

use App\Localization\Entity\Currency;
use App\Subscription\Entity\Subscription;
use App\Subscription\Entity\SubscriptionCycle;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use App\Payment\Repository\InvoiceRepository;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: "invoice")]
class Invoice implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $subscriptionId;

    #[ORM\ManyToOne(targetEntity: Subscription::class)]
    private Subscription $subscription;

    #[ORM\Column(type: "uuid", nullable: true)]
    private ?UuidInterface $subscriptionCycleId = null;

    #[ORM\ManyToOne(targetEntity: SubscriptionCycle::class)]
    private SubscriptionCycle $subscriptionCycle;

    #[ORM\Column(type: "decimal", nullable: false, options:["precision" => 11, "scale" => 2])]
    private Decimal|string|null $totalAmount;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $currencyId;

    #[ORM\ManyToOne(targetEntity: Currency::class)]
    private Currency $currency;

    #[ORM\Column(name: "due_date", type: "date", nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(name: "paid_at", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    #[ORM\Column(name: "overdue_notification_sent_at", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $overdueNotificationSentAt = null;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getSubscriptionId(): UuidInterface
    {
        return $this->subscriptionId;
    }

    public function setSubscriptionId(UuidInterface $subscriptionId): self
    {
        $this->subscriptionId = $subscriptionId;

        return $this;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): self
    {
        $this->subscriptionId = $subscription->getId();
        $this->subscription = $subscription;

        return $this;
    }

    public function getSubscriptionCycleId(): ?UuidInterface
    {
        return $this->subscriptionCycleId;
    }

    public function setSubscriptionCycleId(?UuidInterface $subscriptionCycleId): self
    {
        $this->subscriptionCycleId = $subscriptionCycleId;

        return $this;
    }

    public function getSubscriptionCycle(): SubscriptionCycle
    {
        return $this->subscriptionCycle;
    }

    public function setSubscriptionCycle(SubscriptionCycle $subscriptionCycle): self
    {
        $this->subscriptionCycle = $subscriptionCycle;

        return $this;
    }

    public function getTotalAmount(): Decimal
    {
        return new Decimal($this->totalAmount);
    }

    public function setTotalAmount(Decimal|string $totalAmount): self
    {
        $this->totalAmount = is_string($totalAmount) ? new Decimal($totalAmount) : $totalAmount;

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

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): self
    {
        $this->currency = $currency;
        $this->currencyId = $currency->getId();

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getPaidAt(): ?\DateTimeInterface
    {
        return $this->paidAt;
    }

    public function setPaidAt(?\DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;

        return $this;
    }

    public function getOverdueNotificationSentAt(): ?\DateTimeInterface
    {
        return $this->overdueNotificationSentAt;
    }

    public function setOverdueNotificationSentAt(?\DateTimeInterface $overdueNotificationSentAt): self
    {
        $this->overdueNotificationSentAt = $overdueNotificationSentAt;

        return $this;
    }
}
