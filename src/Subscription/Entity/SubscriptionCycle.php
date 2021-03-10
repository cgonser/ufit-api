<?php

namespace App\Subscription\Entity;

use App\Payment\Entity\Invoice;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Subscription\Repository\SubscriptionCycleRepository")
 * @ORM\Table(name="subscription_cycle")
 * @ORM\HasLifecycleCallbacks()
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class SubscriptionCycle
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
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $subscriptionId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Subscription\Entity\Subscription", inversedBy="cycles")
     * @ORM\JoinColumn(name="subscription_id", referencedColumnName="id")
     */
    private Subscription $subscription;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $invoiceId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Payment\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private Invoice $invoice;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $price;

    /**
     * @ORM\Column(name="starts_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $startsAt = null;

    /**
     * @ORM\Column(name="ends_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $endsAt = null;

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

    public function getPrice(): Decimal
    {
        return new Decimal($this->price);
    }

    public function setPrice(Decimal $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getStartsAt(): ?\DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?\DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?\DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?\DateTimeInterface $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }
}
