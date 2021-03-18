<?php

namespace App\Payment\Service;

use App\Payment\Entity\Payment;
use App\Payment\Service\PaymentProcessor\CreditCardProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Component\String\UnicodeString;

class PaymentProcessor
{
    private PaymentProcessorFactory $paymentProcessorFactory;

    private LoggerInterface $logger;

    public function __construct(PaymentProcessorFactory $paymentProcessorFactory, LoggerInterface $logger)
    {
        $this->paymentProcessorFactory = $paymentProcessorFactory;
        $this->logger = $logger;
    }

    public function process(Payment $payment)
    {
        $processor = $this->paymentProcessorFactory->createProcessor($payment->getPaymentMethod());

        $processor->process($payment);
    }
}