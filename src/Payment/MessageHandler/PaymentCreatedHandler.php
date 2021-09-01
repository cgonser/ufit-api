<?php

declare(strict_types=1);

namespace App\Payment\MessageHandler;

use App\Payment\Entity\Payment;
use App\Payment\Message\PaymentCreatedEvent;
use App\Payment\Provider\PaymentProvider;
use App\Payment\Service\PaymentProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class PaymentCreatedHandler implements MessageHandlerInterface
{
    public function __construct(private PaymentProvider $paymentProvider, private PaymentProcessor $paymentProcessor, private LoggerInterface $logger)
    {
    }

    public function __invoke(PaymentCreatedEvent $paymentCreatedEvent)
    {
        /** @var Payment $payment */
        $payment = $this->paymentProvider->get($paymentCreatedEvent->getPaymentId());

        $this->paymentProcessor->process($payment);

        $this->logger->info(
            'payment.created',
            [
                'id' => $payment->getId()
                    ->toString(),
                'subscriptionId' => $payment->getInvoice()
                    ->getSubscriptionId()
                    ->toString(),
                'customerId' => $payment->getInvoice()
                    ->getSubscription()
                    ->getCustomerId()
                    ->toString(),
                'vendorPlanId' => $payment->getInvoice()
                    ->getSubscription()
                    ->getVendorPlanId()
                    ->toString(),
            ]
        );
    }
}
