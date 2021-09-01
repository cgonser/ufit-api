<?php

declare(strict_types=1);

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Exception\PaymentMissingCreditCardDetailsException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;

class CreditCardProcessor extends PagarmeProcessor implements PaymentProcessorInterface
{
    public function supports(PaymentMethod $paymentMethod): bool
    {
        return 'credit-card' === $paymentMethod->getName();
    }

    protected function validate(Payment $payment): void
    {
        parent::validate($payment);

        $details = $payment->getDetails();

        if (null === $details || !isset($details['card_hash'])) {
            throw new PaymentMissingCreditCardDetailsException();
        }
    }

    /**
     * @return array<string, mixed>
     */
    protected function prepareTransactionData(Payment $payment): array
    {
        return [
            'payment_method' => 'credit_card',
            'capture' => true,
            'card_hash' => $payment->getDetails()['card_hash'],
        ];
    }
}
