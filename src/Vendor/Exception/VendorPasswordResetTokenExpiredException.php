<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Throwable;

class VendorPasswordResetTokenExpiredException extends BadRequestHttpException
{
    /**
     * @var string
     */
    protected $message = 'Vendor password reset token expired';

    public function __construct(string $message = null, Throwable $throwable = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $throwable, $code, $headers);
    }
}
