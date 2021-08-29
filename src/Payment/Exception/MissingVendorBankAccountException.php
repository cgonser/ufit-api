<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class MissingVendorBankAccountException extends PreconditionFailedHttpException
{
    protected $message = 'Missing vendor bank account information';

    public function __construct(?string $message = null)
    {
        parent::__construct($message ? $this->message.': '.$message : $this->message);
    }
}
