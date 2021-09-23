<?php

declare(strict_types=1);

namespace App\Core\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ImageMaxDimensionsExceededException extends BadRequestHttpException
{
    protected $message = 'Image max dimensions exceeded. Use images smaller than (%s x %s)';

    public function __construct(string $maxWidth, string $maxHeight)
    {
        parent::__construct(sprintf($this->message, $maxWidth, $maxHeight));
    }
}
