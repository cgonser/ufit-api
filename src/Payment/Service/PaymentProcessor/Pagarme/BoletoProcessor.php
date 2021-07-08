<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;

class BoletoProcessor extends PagarmeProcessor implements PaymentProcessorInterface
{
    protected function prepareTransactionData(Payment $payment): array
    {
        return [
            'payment_method' => 'boleto',
            'capture' => true,
        ];
    }

    public function supports(PaymentMethod $paymentMethod): bool
    {
        return 'boleto' === $paymentMethod->getName();
    }
}
