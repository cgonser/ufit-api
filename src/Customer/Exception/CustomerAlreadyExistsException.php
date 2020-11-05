<?php

namespace App\Customer\Exception;

class CustomerAlreadyExistsException extends \Exception
{
    protected $message = "Customer already exists";
}