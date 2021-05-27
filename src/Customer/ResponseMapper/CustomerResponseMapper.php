<?php

namespace App\Customer\ResponseMapper;

use App\Customer\Dto\CustomerDto;
use App\Customer\Entity\Customer;

class CustomerResponseMapper
{
    public function map(Customer $customer): CustomerDto
    {
        $customerDto = new CustomerDto();
        $customerDto->id = $customer->getId();
        $customerDto->name = $customer->getName();
        $customerDto->email = $customer->getEmail();
        $customerDto->phone = $customer->getPhone();
        $customerDto->gender = $customer->getGender();
        $customerDto->height = $customer->getHeight();
        $customerDto->lastWeight = null !== $customer->getLastWeight() ? $customer->getLastWeight()->toString() : null;
        $customerDto->birthDate = $customer->getBirthDate()
            ? $customer->getBirthDate()->format('Y-m-d')
            : null;
        $customerDto->goals = $customer->getGoals();
        $customerDto->country = $customer->getCountry();
        $customerDto->locale = $customer->getLocale();
        $customerDto->timezone = $customer->getTimezone();
        $customerDto->documents = $customer->getDocuments();

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