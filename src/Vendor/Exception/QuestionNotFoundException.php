<?php

namespace App\Vendor\Exception;

class QuestionNotFoundException extends \Exception
{
    protected $message = "Question not found";
}