<?php

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class InvoiceNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Invoice not found';
}
