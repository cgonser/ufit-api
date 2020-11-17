<?php

namespace App\Customer\Exception;

class CustomerPhotoInvalidTakenAtException extends \Exception
{
    protected $message = "Invalid takenAt value";
}
