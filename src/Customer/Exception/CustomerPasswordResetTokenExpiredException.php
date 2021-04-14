<?php

namespace App\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerPasswordResetTokenExpiredException extends BadRequestHttpException
{
    protected $message = 'Customer password reset token expired';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
