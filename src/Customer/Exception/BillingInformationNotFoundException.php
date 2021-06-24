<?php

namespace App\Customer\Exception;

use App\Core\Exception\ResourceNotFoundException;

class BillingInformationNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Billing information not found';
}
