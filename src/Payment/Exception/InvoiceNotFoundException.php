<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\ResourceNotFoundException;

class InvoiceNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Invoice not found';
}
