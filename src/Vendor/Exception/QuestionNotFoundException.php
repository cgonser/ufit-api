<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class QuestionNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Question not found';
}
