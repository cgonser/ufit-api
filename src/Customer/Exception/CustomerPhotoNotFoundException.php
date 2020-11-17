<?php

namespace App\Customer\Exception;

class CustomerPhotoNotFoundException extends \Exception
{
    protected $message = 'Photo not found';
}
