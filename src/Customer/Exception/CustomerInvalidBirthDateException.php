<?php

namespace App\Customer\Exception;

class CustomerInvalidBirthDateException extends \Exception
{
    protected $message = "Invalid birth date";
}