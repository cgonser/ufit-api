<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class QuestionnaireNotFoundException extends ResourceNotFoundException
{
    protected $message = "Questionnaire not found";
}