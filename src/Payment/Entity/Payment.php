<?php

namespace App\Payment\Entity;

use App\Core\Entity\Currency;
use App\Core\Entity\PaymentMethod;
use App\Customer\Entity\Customer;
use App\Subscription\Entity\SubscriptionCycle;
use App\Vendor\Entity\Vendor;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity(repositoryClass="App\Payment\Repository\PaymentRepository")
 * @ORM\Table(name="payment")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Payment
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Customer\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id")
     */
    private Customer $customer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Vendor\Entity\Vendor")
     * @ORM\JoinColumn(name="vendor_id", referencedColumnName="id")
     */
    private Vendor $vendor;

    /**
     * @ORM\ManyToOne(targetEntity="App\Subscription\Entity\SubscriptionCycle")
     * @ORM\JoinColumn(name="subscription_cycle_id", referencedColumnName="id")
     */
    private SubscriptionCycle $subscriptionCycle;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="payment_method_id", referencedColumnName="id")
     */
    private PaymentMethod $paymentMethod;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\Currency")
     * @ORM\JoinColumn(name="curency_id", referencedColumnName="id")
     */
    private Currency $currency;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $status;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $amount;

    /**
     * @ORM\Column(name="due_date", type="date", nullable=false)
     */
    private \DateTimeInterface $dueDate;

    /**
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $paidAt = null;

    /**
     * @ORM\Column(name="overdue_notification_sent_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $overdueNotificationSentAt = null;

    /**
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $deletedAt = null;
}
