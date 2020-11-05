<?php

namespace App\Customer\Exception;

class CustomerInvalidPasswordException extends \Exception
{
    protected $message = "Invalid password";
}