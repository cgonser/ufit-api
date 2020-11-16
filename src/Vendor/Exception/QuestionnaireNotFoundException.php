<?php

namespace App\Vendor\Exception;

class QuestionnaireNotFoundException extends \Exception
{
    protected $message = "Questionnaire not found";
}