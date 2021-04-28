<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Exception\PaymentMissingCreditCardDetailsException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;
use Decimal\Decimal;

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
            'amount' => $transactionInput->amount->mul(100)->toFixed(0),
            'payment_method' => 'credit_card',
            'card_hash' => $transactionInput->cardHash,
            'customer' => [
                'external_id' => $transactionInput->customerId,
                'name' => $transactionInput->customerName,
                'type' => 'individual',
                'country' => 'br',
                'documents' => [
                    [
                        'type' => $transactionInput->customerDocumentType,
                        'number' => $transactionInput->customerDocumentNumber,
                    ],
                ],
                'phone_numbers' => [
                    $transactionInput->customerPhone ?: '+5511989737737',
                ],
                'email' => $transactionInput->customerEmail,
            ],
            'billing' => [
                'name' => $transactionInput->customerName,
                'address' => [
                    'country' => 'br',
                    'street' => 'Avenida Brigadeiro Faria Lima',
                    'street_number' => '1811',
                    'state' => 'sp',
                    'city' => 'Sao Paulo',
                    'neighborhood' => 'Jardim Paulistano',
                    'zipcode' => '01451001',
                ],
            ],
            'items' => [
                [
                    'id' => $transactionInput->productId,
                    'title' => $transactionInput->productName,
                    'unit_price' => $transactionInput->amount->mul(100)->toFixed(0),
                    'quantity' => 1,
                    'tangible' => false,
                ],
            ],
        ];
    }

    public function supports(PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->getName() === 'credit-card';
    }
}