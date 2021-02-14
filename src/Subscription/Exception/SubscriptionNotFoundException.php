<?php

namespace App\Subscription\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionNotFoundException extends NotFoundHttpException
{
    protected $message = 'Subscription not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
