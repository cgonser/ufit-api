<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class VendorEmailAddressInUseException extends BadRequestHttpException
{
    /**
     * @var string
     */
    protected $message = 'E-mail address already in use';
}
