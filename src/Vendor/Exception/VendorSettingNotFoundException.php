<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorSettingNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Vendor setting not found';
}
