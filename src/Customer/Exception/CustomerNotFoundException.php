<?php

namespace App\Customer\Exception;

class CustomerNotFoundException extends \Exception
{
    protected $message = "Customer not found";
}