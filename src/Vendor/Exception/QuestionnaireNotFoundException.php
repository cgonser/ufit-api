<?php

declare(strict_types=1);

namespace App\Vendor\Exception;

use App\Core\Exception\ResourceNotFoundException;

class QuestionnaireNotFoundException extends ResourceNotFoundException
{
    /**
     * @var string
     */
    protected $message = 'Questionnaire not found';
}
