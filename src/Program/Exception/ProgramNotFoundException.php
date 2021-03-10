<?php

namespace App\Program\Exception;

use App\Core\Exception\ResourceNotFoundException;

class ProgramNotFoundException extends ResourceNotFoundException
{
    protected $message = 'Program not found';
}
