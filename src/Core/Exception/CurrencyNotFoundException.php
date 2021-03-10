<?php

namespace App\Core\Exception;

class CurrencyNotFoundException extends ResourceNotFoundException
{
    protected $message = "Currency not found";
}