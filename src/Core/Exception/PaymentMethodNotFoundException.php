<?php

namespace App\Core\Exception;

class PaymentMethodNotFoundException extends ResourceNotFoundException
{
    protected $message = "Payment method not found";
}
