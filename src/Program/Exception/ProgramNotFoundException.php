<?php

namespace App\Program\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProgramNotFoundException extends NotFoundHttpException
{
    protected $message = 'Program not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
