<?php

namespace App\Program\Exception;

use App\Core\Exception\ResourceNotFoundException;

class ProgramAssetNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Program asset not found';
}
