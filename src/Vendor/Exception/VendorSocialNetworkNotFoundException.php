<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class VendorSocialNetworkNotFoundException extends NotFoundHttpException
{
    /**
     * @var string
     */
    protected $message = 'Vendor social network integration not found';

    public function __construct(string $message = null, Throwable $throwable = null, int $code = 0, array $headers = [])
    {
        parent::__construct($message ?? $this->message, $throwable, $code, $headers);
    }
}
