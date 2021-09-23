<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorInvalidPhotoException extends InvalidInputException
{
    /**
     * @var string
     */
    protected $message = 'Invalid photo file';
}
