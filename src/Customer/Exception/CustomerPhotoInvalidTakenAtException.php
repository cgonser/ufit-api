<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerPhotoInvalidTakenAtException extends BadRequestHttpException
{
    protected $message = 'Invalid takenAt value';
}
