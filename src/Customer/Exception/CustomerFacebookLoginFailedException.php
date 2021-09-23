<?php

declare(strict_types=1);

namespace App\Customer\Exception;

use App\Core\Exception\ApiJsonException;
use Symfony\Component\HttpFoundation\Response;

class CustomerFacebookLoginFailedException extends ApiJsonException
{
    protected $message = 'Unable to authenticate customer with facebook access token';

    public function __construct()
    {
        parent::__construct(Response::HTTP_UNAUTHORIZED, $this->message);
    }
}
