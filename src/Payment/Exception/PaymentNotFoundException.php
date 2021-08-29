<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PaymentNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Payment not found';
}
