<?php

namespace App\Program\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProgramAssignmentNotFoundException extends NotFoundHttpException
{
    protected $message = 'Program assignment not found';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
