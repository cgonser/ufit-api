<?php

namespace App\Customer\Exception;

class MeasureTypeNotFoundException extends \Exception
{
    protected $message = "Measure Type not found";
}