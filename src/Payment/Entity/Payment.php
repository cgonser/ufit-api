<?php

declare(strict_types=1);

namespace App\Payment\Entity;

use App\Customer\Entity\BillingInformation;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SoftDeletableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\SoftDeletable\SoftDeletableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use App\Payment\Repository\PaymentRepository;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
#[ORM\Table(name: "payment")]
class Payment implements SoftDeletableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SoftDeletableTrait;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_REJECTED = 'rejected';

    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface $id;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $invoiceId;

    #[ORM\ManyToOne(targetEntity: Invoice::class)]
    private Invoice $invoice;

    #[ORM\Column(type: "uuid")]
    private UuidInterface $paymentMethodId;

    #[ORM\ManyToOne(targetEntity: PaymentMethod::class)]
    private PaymentMethod $paymentMethod;

    #[ORM\Column(type: "uuid", nullable: true)]
    private ?UuidInterface $billingInformationId = null;

    #[ORM\ManyToOne(targetEntity: BillingInformation::class)]
    #[ORM\JoinColumn(name: "billing_information_id", referencedColumnName: "id", nullable: true)]
    private ?BillingInformation $billingInformation = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: "decimal", nullable: false, options: ["precision" => 11, "scale" => 2])]
    private Decimal|string|null $amount = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $details = null;

    #[ORM\Column(type: "json", nullable: true)]
    private ?array $gatewayResponse = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $externalReference = null;

    #[ORM\Column(name: "due_date", type: "date", nullable: true)]
    private ?\DateTimeInterface $dueDate = null;

    #[ORM\Column(name: "paid_at", type: "datetime", nullable: true)]
    private ?\DateTimeInterface $paidAt = null;

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getInvoiceId(): UuidInterface
    {
        return $this->invoiceId;
    }

    public function setInvoiceId(UuidInterface $invoiceId): self
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    public function getInvoice(): Invoice
    {
        return $this->invoice;
    }

    public function setInvoice(Invoice $invoice): self
    {
        $this->invoiceId = $invoice->getId();
        $this->invoice = $invoice;

        return $this;
    }

    public function getBillingInformationId(): ?UuidInterface
    {
        return $this->billingInformationId;
    }

    public function setBillingInformationId(?UuidInterface $billingInformationId): self
    {
        $this->billingInformationId = $billingInformationId;

        return $this;
    }

    public function getBillingInformation(): ?BillingInformation
    {
        return $this->billingInformation;
    }

    public function setBillingInformation(?BillingInformation $billingInformation): self
    {
        $this->billingInformation = $billingInformation;
        $this->billingInformationId = null !== $billingInformation ? $billingInformation->getId() : null;

        return $this;
    }

    public function getPaymentMethodId(): UuidInterface
    {
        return $this->paymentMethodId;
    }

    public function setPaymentMethodId(UuidInterface $paymentMethodId): self
    {
        $this->paymentMethodId = $paymentMethodId;

        return $this;
    }

    public function getPaymentMethod(): PaymentMethod
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(PaymentMethod $paymentMethod): self
    {
        $this->paymentMethodId = $paymentMethod->getId();
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAmount(): ?Decimal
    {
        return null !== $this->amount ? new Decimal($this->amount) : null;
    }

    public function setAmount(Decimal|string $amount): self
    {
        $this->amount = is_string($amount) ? new Decimal($amount) : $amount;

        return $this;
    }

    public function getDetails(): ?array
    {
        return $this->details;
    }

    public function setDetails(?array $details): self
    {
        $this->details = $details;

        return $this;
    }

    public function getGatewayResponse(): ?array
    {
        return $this->gatewayResponse;
    }

    public function setGatewayResponse(?array $gatewayResponse): self
    {
        $this->gatewayResponse = $gatewayResponse;

        return $this;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function setExternalReference(?string $externalReference): self
    {
        $this->externalReference = $externalReference;

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
}
