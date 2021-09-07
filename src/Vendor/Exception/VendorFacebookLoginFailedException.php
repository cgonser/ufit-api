<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ApiJsonException;
use Symfony\Component\HttpFoundation\Response;

class VendorFacebookLoginFailedException extends ApiJsonException
{
    protected $message = 'Unable to authenticate user with facebook access token';

    public function __construct()
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $this->message);
    }
}
