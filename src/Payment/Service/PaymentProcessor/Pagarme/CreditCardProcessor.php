<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Exception\PaymentMissingCreditCardDetailsException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;

class CreditCardProcessor extends PagarmeProcessor implements PaymentProcessorInterface
{
    public function prepareTransactionInput(Payment $payment)
    {
        $transactionInput = parent::prepareTransactionInput($payment);

        $details = $payment->getDetails();
        $transactionInput->cardHash = $details['card_hash'];

        return $transactionInput;
    }

    protected function validate(Payment $payment)
    {
        parent::validate($payment);

        $details = $payment->getDetails();

        if (null === $details || !isset($details['card_hash'])) {
            throw new PaymentMissingCreditCardDetailsException();
        }
    }

    protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array
    {
        return [
            'payment_method' => 'credit_card',
            'capture' => true,
            'card_hash' => $transactionInput->cardHash,
        ];
    }

    public function supports(PaymentMethod $paymentMethod): bool
    {
        return 'credit-card' === $paymentMethod->getName();
    }
}
