<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerSocialNetworkNotFoundException extends NotFoundHttpException
{
    protected $message = 'Customer social network integration not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
