<?php

declare(strict_types=1);

namespace App\Payment\Service;

use App\Payment\Entity\Payment;
use Psr\Log\LoggerInterface;

class PaymentProcessor
{
    public function __construct(private PaymentProcessorFactory $paymentProcessorFactory)
    {
    }

    public function process(Payment $payment): void
    {
        $paymentProcessor = $this->paymentProcessorFactory->createProcessor($payment->getPaymentMethod());

        $paymentProcessor->process($payment);
    }
}
