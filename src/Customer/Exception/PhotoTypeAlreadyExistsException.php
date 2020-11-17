<?php

namespace App\Customer\Exception;

class PhotoTypeAlreadyExistsException extends \Exception
{
    protected $message = "Photo Type already exists";
}