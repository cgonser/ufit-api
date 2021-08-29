<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\InvalidInputException;

class PaymentProcessorNotFoundException extends InvalidInputException
{
    protected $message = 'Processor not found for payment method %s';

    public function __construct(string $paymentMethodName)
    {
        parent::__construct(sprintf($this->message, $paymentMethodName));
    }
}
