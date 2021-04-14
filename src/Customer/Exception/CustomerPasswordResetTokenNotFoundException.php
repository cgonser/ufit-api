<?php

namespace App\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerPasswordResetTokenNotFoundException extends NotFoundHttpException
{
    protected $message = 'Customer password reset token not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
