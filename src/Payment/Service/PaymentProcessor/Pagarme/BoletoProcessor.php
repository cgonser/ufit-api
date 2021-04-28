<?php

namespace App\Payment\Service\PaymentProcessor\Pagarme;

use App\Core\Exception\InvalidInputException;
use App\Payment\Dto\PagarmeTransactionInputDto;
use App\Payment\Entity\Payment;
use App\Payment\Entity\PaymentMethod;
use App\Payment\Exception\PaymentMissingCreditCardDetailsException;
use App\Payment\Service\PaymentProcessor\PaymentProcessorInterface;
use Decimal\Decimal;

class BoletoProcessor extends PagarmeProcessor implements PaymentProcessorInterface
{
    protected function prepareTransactionData(PagarmeTransactionInputDto $transactionInput): array
    {
        return [
            'amount' => $transactionInput->amount->mul(100)->toFixed(0),
            'payment_method' => 'boleto',
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
        return $paymentMethod->getName() === 'boleto';
    }
}