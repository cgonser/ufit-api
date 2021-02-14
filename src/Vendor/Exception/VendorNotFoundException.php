<?php

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VendorNotFoundException extends NotFoundHttpException
{
    protected $message = 'Vendor not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
