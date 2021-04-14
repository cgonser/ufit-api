<?php

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VendorEmailAddressInUseException extends BadRequestHttpException
{
    protected $message = "E-mail address already in use";
}