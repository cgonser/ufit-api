<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class QuestionNotFoundException extends ResourceNotFoundException
{
    protected $message = "Question not found";
}