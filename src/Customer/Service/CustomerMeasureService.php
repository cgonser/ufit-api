<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasure;
use App\Customer\Exception\CustomerMeasureInvalidTakenAtException;
use App\Customer\Provider\CustomerMeasureProvider;
use App\Customer\Repository\CustomerMeasureRepository;
use App\Customer\Request\CustomerMeasureRequest;

class CustomerMeasureService
{
    private CustomerMeasureRepository $customerMeasureRepository;

    private CustomerMeasureProvider $customerMeasureProvider;

    public function __construct(
        CustomerMeasureRepository $customerMeasureRepository,
        CustomerMeasureProvider $customerMeasureProvider
    ) {
        $this->customerMeasureRepository = $customerMeasureRepository;
        $this->customerMeasureProvider = $customerMeasureProvider;
    }

    public function create(Customer $customer, CustomerMeasureRequest $customerMeasureRequest): CustomerMeasure
    {
        $customerMeasure = new CustomerMeasure();
        $customerMeasure->setCustomer($customer);

        $this->mapFromRequest($customerMeasure, $customerMeasureRequest);

        $this->customerMeasureRepository->save($customerMeasure);

        return $customerMeasure;
    }

    public function update(CustomerMeasure $customerMeasure, CustomerMeasureRequest $customerMeasureRequest)
    {
        $this->mapFromRequest($customerMeasure, $customerMeasureRequest);

        $this->customerMeasureRepository->save($customerMeasure);
    }

    private function mapFromRequest(CustomerMeasure $customerMeasure, CustomerMeasureRequest $customerMeasureRequest)
    {
        $customerMeasure->setNotes($customerMeasureRequest->notes);

        if (null !== $customerMeasureRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat($customerMeasureRequest->takenAt, \DateTimeInterface::ISO8601);

            if (false === $takenAt) {
                throw new CustomerMeasureInvalidTakenAtException();
            }

            $customerMeasure->setTakenAt($takenAt);
        }
    }
}
