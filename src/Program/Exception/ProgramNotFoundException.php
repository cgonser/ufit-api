<?php

declare(strict_types=1);

namespace App\Program\Exception;

use App\Core\Exception\ResourceNotFoundException;

class ProgramNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Program not found';
}
