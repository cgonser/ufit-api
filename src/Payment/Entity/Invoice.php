<?php

namespace App\Payment\Entity;

use App\Core\Entity\Currency;
use App\Subscription\Entity\Subscription;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Payment\Repository\InvoiceRepository")
 * @ORM\Table(name="invoice")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Invoice
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $subscriptionId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Subscription\Entity\Subscription", inversedBy="cycles")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private Subscription $subscription;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $totalAmount;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $currencyId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Currency")
     * @ORM\JoinColumn(name="curency_id", referencedColumnName="id")
     */
    private Currency $currency;

    /**
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $dueDate = null;

    /**
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $paidAt = null;

    /**
     * @ORM\Column(name="overdue_notification_sent_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $overdueNotificationSentAt = null;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $deletedAt = null;

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
        $this->subscription = $subscription;

        return $this;
    }

    public function getTotalAmount(): Decimal
    {
        return new Decimal($this->totalAmount);
    }

    public function setTotalAmount(Decimal $totalAmount): self
    {
        $this->totalAmount = $totalAmount;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
