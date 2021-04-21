<?php

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class PaymentMethodNotFoundException extends ResourceNotFoundException
{
    protected $message = "Payment method not found";
}
