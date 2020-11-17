<?php

namespace App\Core\Exception;

class CurrencyAlreadyExistsException extends \Exception
{
    protected $message = "Currency already exists";
}