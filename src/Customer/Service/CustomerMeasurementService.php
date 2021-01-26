<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Entity\CustomerMeasurementItem;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Exception\CustomerMeasurementItemInvalidUnitException;
use App\Customer\Provider\CustomerMeasurementItemProvider;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Repository\CustomerMeasurementItemRepository;
use App\Customer\Repository\CustomerMeasurementRepository;
use App\Customer\Request\CustomerMeasurementItemRequest;
use App\Customer\Request\CustomerMeasurementRequest;

class CustomerMeasurementService
{
    private CustomerMeasurementRepository $customerMeasurementRepository;

    private CustomerMeasurementProvider $customerMeasurementProvider;

    private CustomerMeasurementItemRepository $customerMeasurementItemRepository;

    private CustomerMeasurementItemProvider $customerMeasurementItemProvider;

    private MeasurementTypeProvider $measurementTypeProvider;

    public function __construct(
        CustomerMeasurementRepository $customerMeasurementRepository,
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementItemRepository $customerMeasurementItemRepository,
        CustomerMeasurementItemProvider $customerMeasurementItemProvider,
        MeasurementTypeProvider $measurementTypeProvider
    ) {
        $this->customerMeasurementRepository = $customerMeasurementRepository;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerMeasurementItemProvider = $customerMeasurementItemProvider;
        $this->measurementTypeProvider = $measurementTypeProvider;
        $this->customerMeasurementItemRepository = $customerMeasurementItemRepository;
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

    public function delete(CustomerMeasurement $customerMeasurement)
    {
        $this->customerMeasurementRepository->delete($customerMeasurement);
    }

    private function mapFromRequest(CustomerMeasurement $customerMeasurement, CustomerMeasurementRequest $customerMeasurementRequest)
    {
        $customerMeasurement->setNotes($customerMeasurementRequest->notes);

        if (null !== $customerMeasurementRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $customerMeasurementRequest->takenAt);

            if (false === $takenAt) {
                throw new CustomerMeasurementInvalidTakenAtException();
            }

            $customerMeasurement->setTakenAt($takenAt);
        } elseif (null === $customerMeasurement->getTakenAt()) {
            $customerMeasurement->setTakenAt(new \DateTime());
        }

        if (count($customerMeasurementRequest->items) > 0) {
            $this->mapItemsFromRequest($customerMeasurement, $customerMeasurementRequest->items);
        }
    }

    private function mapItemsFromRequest(CustomerMeasurement $customerMeasurement, array $items)
    {
        /** @var CustomerMeasurementItemRequest $customerMeasurementItemRequest */
        foreach ($items as $customerMeasurementItemRequest) {
            $measurementType = $this->measurementTypeProvider->getBySlug(
                $customerMeasurementItemRequest->type
            );

            $customerMeasurementItem = $this->customerMeasurementItemProvider->findOneByCustomerMeasurementAndType(
                $customerMeasurement, $measurementType
            );

            if (null === $customerMeasurementItem) {
                $customerMeasurementItem = new CustomerMeasurementItem();
                $customerMeasurementItem->setMeasurementType($measurementType);
            }

            $customerMeasurementItem->setMeasurement($customerMeasurementItemRequest->measurement);

            if (!$measurementType->isUnitValid($customerMeasurementItemRequest->unit)) {
                throw new CustomerMeasurementItemInvalidUnitException();
            }

            $customerMeasurementItem->setUnit($customerMeasurementItemRequest->unit);

            $customerMeasurement->addItem($customerMeasurementItem);
        }
    }
}
