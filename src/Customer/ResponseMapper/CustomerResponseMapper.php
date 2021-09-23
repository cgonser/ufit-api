<?php

declare(strict_types=1);

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;

class CustomerResponseMapper
{
    public function map(Customer $customer): CustomerDto
    {
        $customerDto = new CustomerDto();
        $customerDto->id = $customer->getId()->toString();
        $customerDto->name = $customer->getName();
        $customerDto->email = $customer->getEmail();
        $customerDto->phoneIntlCode = $customer->getPhoneIntlCode();
        $customerDto->phoneAreaCode = $customer->getPhoneAreaCode();
        $customerDto->phoneNumber = $customer->getPhoneNumber();
        $customerDto->gender = $customer->getGender();
        $customerDto->height = $customer->getHeight();
        $customerDto->lastWeight = $customer->getLastWeight()?->toString();
        $customerDto->birthDate = $customer->getBirthDate()?->format('Y-m-d');
        $customerDto->goals = $customer->getGoals();
        $customerDto->country = $customer->getCountry();
        $customerDto->locale = $customer->getLocale();
        $customerDto->timezone = $customer->getTimezone();
        $customerDto->documents = $customer->getDocuments();
        $customerDto->isPasswordDefined = null !== $customer->getPassword();

        return $customerDto;
    }

    public function mapMultiple(array $customers): array
    {
        $customerDtos = [];

        foreach ($customers as $customer) {
            $customerDtos[] = $this->map($customer);
        }

        return $customerDtos;
    }
}
