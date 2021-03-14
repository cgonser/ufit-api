<?php

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PaymentNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Payment not found';
}
