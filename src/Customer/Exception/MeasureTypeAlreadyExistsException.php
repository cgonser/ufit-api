<?php

namespace App\Customer\Exception;

class MeasureTypeAlreadyExistsException extends \Exception
{
    protected $message = "Measure Type already exists";
}