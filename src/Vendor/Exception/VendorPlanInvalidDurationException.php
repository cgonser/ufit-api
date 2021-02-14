<?php

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VendorPlanInvalidDurationException extends BadRequestHttpException
{
    protected $message = 'The duration informed is not valid';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
