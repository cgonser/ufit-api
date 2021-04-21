<?php

namespace App\Payment\Service\PaymentProcessor;

use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;

interface PaymentProcessorInterface
{
    public function process(Payment $payment);

    public function supports(PaymentMethod $paymentMethod): bool;
}
