<?php

namespace App\Vendor\Exception;

class VendorInstagramLoginMissingEmailException extends \Exception
{
    protected $message = 'Missing vendor e-mail to be able to successfully authenticate';
}