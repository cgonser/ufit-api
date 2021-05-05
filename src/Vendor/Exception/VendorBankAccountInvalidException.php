<?php

namespace App\Vendor\Exception;

use App\Core\Exception\ApiJsonInputValidationException;
use App\Vendor\Entity\VendorBankAccount;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class VendorBankAccountInvalidException extends ApiJsonInputValidationException
{
    public function __construct(VendorBankAccount $vendorBankAccount, ?string $property = null, ?string $errorMessage = null)
    {
        parent::__construct(
            new ConstraintViolationList([
                new ConstraintViolation(
                    $errorMessage,
                    $errorMessage,
                    [],
                    $vendorBankAccount,
                    $property,
                    null
                ),
            ])
        );
    }
}
