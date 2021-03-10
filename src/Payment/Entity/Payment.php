<?php

namespace App\Payment\Entity;

use App\Core\Entity\PaymentMethod;
use Decimal\Decimal;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Payment\Repository\PaymentRepository")
 * @ORM\Table(name="payment")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", hardDelete=false)
 */
class Payment
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
    private UuidInterface $invoiceId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Payment\Entity\Invoice")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id")
     */
    private Invoice $invoice;

    /**
     * @ORM\Column(type="uuid", unique=true)
     */
    private UuidInterface $paymentMethodId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Core\Entity\PaymentMethod")
     * @ORM\JoinColumn(name="payment_method_id", referencedColumnName="id")
     */
    private PaymentMethod $paymentMethod;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private string $status;

    /**
     * @ORM\Column(type="decimal", nullable=false, options={"precision": 11, "scale": 2})
     */
    private string $amount;

    /**
     * @ORM\Column(name="due_date", type="date", nullable=true)
     */
    private ?\DateTimeInterface $dueDate = null;

    /**
     * @ORM\Column(name="paid_at", type="datetime", nullable=true)
     */
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
        $this->invoice = $invoice;

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
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getAmount(): Decimal
    {
        return new Decimal($this->amount);
    }

    public function setAmount(Decimal $amount): self
    {
        $this->amount = $amount;

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
