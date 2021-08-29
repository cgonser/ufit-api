<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VendorPlanInvalidDurationException extends BadRequestHttpException
{
    /**
     * @var string
     */
    protected $message = 'The duration informed is not valid';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
