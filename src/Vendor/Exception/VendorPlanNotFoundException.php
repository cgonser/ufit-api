<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class VendorPlanNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Plan not found';
}
