<?php

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorSlugInUseException extends InvalidInputException
{
    protected $message = "Slug already in use";
}
