<?php

declare(strict_types=1);

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageMaxSizeExceededException extends BadRequestHttpException
{
    protected $message = 'Image max size exceeded';

    public function __construct(string $message = null, \Throwable $previous = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $previous, $code, $headers);
    }
}
