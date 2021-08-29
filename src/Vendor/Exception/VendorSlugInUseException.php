<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\InvalidInputException;

class VendorSlugInUseException extends InvalidInputException
{
    /**
     * @var string
     */
    protected $message = 'Slug already in use';
}
