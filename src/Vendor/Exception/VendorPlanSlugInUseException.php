<?php

namespace App\Vendor\Exception;

class VendorPlanSlugInUseException extends \Exception
{
    protected $message = "Slug already in use";
}