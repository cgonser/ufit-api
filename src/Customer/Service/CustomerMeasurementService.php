<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Repository\CustomerMeasurementRepository;
use App\Customer\Request\CustomerMeasurementRequest;

class CustomerMeasurementService
{
    private CustomerMeasurementRepository $customerMeasurementRepository;

    private CustomerMeasurementProvider $customerMeasurementProvider;

    public function __construct(
        CustomerMeasurementRepository $customerMeasurementRepository,
        CustomerMeasurementProvider $customerMeasurementProvider
    ) {
        $this->customerMeasurementRepository = $customerMeasurementRepository;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
    }

    public function create(Customer $customer, CustomerMeasurementRequest $customerMeasurementRequest): CustomerMeasurement
    {
        $customerMeasurement = new CustomerMeasurement();
        $customerMeasurement->setCustomer($customer);

        $this->mapFromRequest($customerMeasurement, $customerMeasurementRequest);

        $this->customerMeasurementRepository->save($customerMeasurement);

        return $customerMeasurement;
    }

    public function update(CustomerMeasurement $customerMeasurement, CustomerMeasurementRequest $customerMeasurementRequest)
    {
        $this->mapFromRequest($customerMeasurement, $customerMeasurementRequest);

        $this->customerMeasurementRepository->save($customerMeasurement);
    }

    private function mapFromRequest(CustomerMeasurement $customerMeasurement, CustomerMeasurementRequest $customerMeasurementRequest)
    {
        $customerMeasurement->setNotes($customerMeasurementRequest->notes);

        if (null !== $customerMeasurementRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat($customerMeasurementRequest->takenAt, \DateTimeInterface::ISO8601);

            if (false === $takenAt) {
                throw new CustomerMeasurementInvalidTakenAtException();
            }

            $customerMeasurement->setTakenAt($takenAt);
        }
    }
}
