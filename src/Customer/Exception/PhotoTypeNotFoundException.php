<?php

namespace App\Customer\Exception;

class PhotoTypeNotFoundException extends \Exception
{
    protected $message = 'Photo Type not found';
}
