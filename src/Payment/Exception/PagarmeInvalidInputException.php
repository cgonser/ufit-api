<?php

declare(strict_types=1);

namespace App\Payment\Exception;

use App\Core\Exception\ApiJsonInputValidationException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class PagarmeInvalidInputException extends ApiJsonInputValidationException
{
    public function __construct(object $entity, ?string $property = null, ?string $errorMessage = null)
    {
        parent::__construct(
            new ConstraintViolationList([
                new ConstraintViolation($errorMessage, $errorMessage, [], $entity, $property, null),
            ])
        );
    }
}
