<?php

declare(strict_types=1);

namespace App\Program\Exception;

use App\Core\Exception\ResourceNotFoundException;

class ProgramAssignmentNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Program assignment not found';
}
