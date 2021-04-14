<?php

namespace App\Customer\Exception;

use App\Core\Exception\ApiJsonException;
use Symfony\Component\HttpFoundation\Response;

class CustomerGoogleLoginFailedException extends ApiJsonException
{
    protected $message = 'Unable to authenticate customer with google access token';

    public function __construct()
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $this->message);
    }
}
