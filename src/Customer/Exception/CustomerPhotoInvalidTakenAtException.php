<?php

namespace App\Customer\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomerPhotoInvalidTakenAtException extends BadRequestHttpException
{
    protected $message = "Invalid takenAt value";
}
