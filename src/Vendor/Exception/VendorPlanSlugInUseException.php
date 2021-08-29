<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Exception;

class VendorPlanSlugInUseException extends Exception
{
    /**
     * @var string
     */
    protected $message = 'Slug already in use';
}
