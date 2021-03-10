<?php

namespace App\Core\Validation;

use App\Core\Exception\InvalidEntityException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EntityValidator
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function validate(object $entityObject)
    {
        $errors = $this->validator->validate($entityObject);

        if ($errors->count() > 0) {
            throw new InvalidEntityException($errors);
        }
    }
}