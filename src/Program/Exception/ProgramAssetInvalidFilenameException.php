<?php

namespace App\Program\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ProgramAssetInvalidFilenameException extends BadRequestHttpException
{
    protected $message = 'Invalid or null filename';

    public function __construct()
    {
        parent::__construct($this->message);
    }
}
