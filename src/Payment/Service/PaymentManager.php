<?php

declare(strict_types=1);

namespace App\Payment\Service;

use DateTime;
use DateTimeImmutable;
use App\Payment\Entity\Invoice;
use App\Payment\Entity\Payment;
//use App\Payment\Message\PaymentCreatedEvent;
//use App\Payment\Message\PaymentDeletedEvent;
//use App\Payment\Message\PaymentUpdatedEvent;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Message\InvoicePaidEvent;
use App\Payment\Message\PaymentCreatedEvent;
use App\Payment\Message\PaymentUpdatedEvent;
use App\Payment\Repository\PaymentRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class PaymentManager
{
    public function __construct(private PaymentRepository $paymentRepository, private MessageBusInterface $messageBus)
    {
    }

    public function create(Payment $payment): void
    {
        if (null === $payment->getStatus()) {
            $payment->setStatus(Payment::STATUS_PENDING);
        }

        $this->save($payment);

        $this->messageBus->dispatch(new PaymentCreatedEvent($payment->getId()));
    }

    public function update(Payment $payment): void
    {
        $this->save($payment);

        $this->messageBus->dispatch(new PaymentUpdatedEvent($payment->getId()));
    }

    public function save(Payment $payment): void
    {
        $this->paymentRepository->save($payment);
    }

    public function delete(Payment $payment): void
    {
        $this->paymentRepository->delete($payment);

//        $this->messageBus->dispatch(new PaymentDeletedEvent($payment->getId()));
    }

    public function markAsPaid(Payment $payment, DateTime|DateTimeImmutable $paidAt): void
    {
        $payment->setStatus(Payment::STATUS_PAID);
        $payment->setPaidAt($paidAt);

        $this->paymentRepository->save($payment);

        $this->messageBus->dispatch(new InvoicePaidEvent($payment->getInvoiceId(), $payment->getPaidAt()));
    }

    public function createFromInvoice(Invoice $invoice, PaymentMethod $paymentMethod): Payment
    {
        $payment = new Payment();
        $payment->setInvoice($invoice);
        $payment->setInvoiceId($invoice->getId());
        $payment->setPaymentMethod($paymentMethod);
        $payment->setAmount($invoice->getTotalAmount());
        $payment->setDueDate($invoice->getDueDate());

        $this->save($payment);

        return $payment;
    }
}
