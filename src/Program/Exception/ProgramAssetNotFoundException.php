<?php

declare(strict_types=1);

namespace App\Program\Exception;

use App\Core\Exception\ResourceNotFoundException;

class ProgramAssetNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Program asset not found';
}
