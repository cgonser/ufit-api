<?php

namespace App\Customer\Exception;

class CustomerEmailAddressInUseException extends \Exception
{
    protected $message = "E-mail address already in use";
}