<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PaymentMethodNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Payment method not found';
}
