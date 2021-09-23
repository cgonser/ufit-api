<?php

declare(strict_types=1);

namespace App\Subscription\Entity;

use DateTimeInterface;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use App\Subscription\Repository\SubscriptionCycleRepository;

#[ORM\Entity(repositoryClass: SubscriptionCycleRepository::class)]
#[ORM\Table(name: 'subscription_cycle')]
#[ORM\HasLifecycleCallbacks()]
class SubscriptionCycle implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $subscriptionId;

    #[ORM\ManyToOne(targetEntity: Subscription::class, inversedBy: 'cycles')]
    private Subscription $subscription;

    #[ORM\Column(type: 'decimal', nullable: false, options: ["precision" => 11, "scale" => 2])]
    private Decimal|string|null $price;

    #[ORM\Column(name: "starts_at", type: "datetime", nullable: true)]
    private ?DateTimeInterface $startsAt = null;

    #[ORM\Column(name: "ends_at", type: "datetime", nullable: true)]
    private ?DateTimeInterface $endsAt = null;

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

    public function setPrice(Decimal|string $price): self
    {
        $this->price = is_string($price) ? new Decimal($price) : $price;

        return $this;
    }

    public function getStartsAt(): ?DateTimeInterface
    {
        return $this->startsAt;
    }

    public function setStartsAt(?DateTimeInterface $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?DateTimeInterface
    {
        return $this->endsAt;
    }

    public function setEndsAt(?DateTimeInterface $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }
}
