<?php

namespace App\Payment\Service\PaymentProcessor;

use App\Core\Entity\PaymentMethod;
use App\Payment\Entity\Payment;

interface PaymentProcessorInterface
{
    public function process(Payment $payment);

    public function supports(PaymentMethod $paymentMethod): bool;
}