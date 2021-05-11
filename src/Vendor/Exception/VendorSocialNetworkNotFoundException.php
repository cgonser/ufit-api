<?php

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VendorSocialNetworkNotFoundException extends NotFoundHttpException
{
    protected $message = 'Vendor social network integration not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
