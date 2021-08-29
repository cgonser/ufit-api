<?php

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorInvalidPhotoException extends InvalidInputException
{
    protected $message = "Invalid photo file";
}
