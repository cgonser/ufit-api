<?php

namespace App\Core\Exception;

class PaymentMethodNotFoundException extends \Exception
{
    protected $message = "Payment method not found";
}
