<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\InvalidInputException;

class PaymentMissingCreditCardDetailsException extends InvalidInputException
{
    protected $message = 'Missing credit card details';
}
