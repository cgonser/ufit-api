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