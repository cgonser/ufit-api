<?php

declare(strict_types=1);

namespace App\Program\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProgramAssetInvalidFilenameException extends BadRequestHttpException
{
    /**
     * @var string
     */
    protected $message = 'Invalid or null filename';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
