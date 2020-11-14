<?php

namespace App\Vendor\Exception;

class VendorSlugInUseException extends \Exception
{
    protected $message = "Slug already in use";
}