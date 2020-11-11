<?php

namespace App\Core\Exception;

class CurrencyNotFoundException extends \Exception
{
    protected $message = "Currency not found";
}