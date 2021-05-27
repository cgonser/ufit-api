<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerMeasurement;
use App\Customer\Entity\CustomerMeasurementItem;
use App\Customer\Exception\CustomerMeasurementInvalidTakenAtException;
use App\Customer\Exception\CustomerMeasurementItemInvalidUnitException;
use App\Customer\Exception\MeasurementTypeNotFoundException;
use App\Customer\Provider\CustomerMeasurementItemProvider;
use App\Customer\Provider\CustomerMeasurementProvider;
use App\Customer\Provider\MeasurementTypeProvider;
use App\Customer\Repository\CustomerMeasurementItemRepository;
use App\Customer\Repository\CustomerMeasurementRepository;
use App\Customer\Request\CustomerMeasurementItemRequest;
use App\Customer\Request\CustomerMeasurementRequest;
use Decimal\Decimal;
use Ramsey\Uuid\Uuid;

class CustomerMeasurementService
{
    private CustomerMeasurementRepository $customerMeasurementRepository;

    private CustomerMeasurementProvider $customerMeasurementProvider;

    private CustomerMeasurementItemRepository $customerMeasurementItemRepository;

    private CustomerMeasurementItemProvider $customerMeasurementItemProvider;

    private MeasurementTypeProvider $measurementTypeProvider;
    private CustomerManager $customerManager;

    public function __construct(
        CustomerMeasurementRepository $customerMeasurementRepository,
        CustomerMeasurementProvider $customerMeasurementProvider,
        CustomerMeasurementItemRepository $customerMeasurementItemRepository,
        CustomerMeasurementItemProvider $customerMeasurementItemProvider,
        CustomerManager $customerManager,
        MeasurementTypeProvider $measurementTypeProvider
    ) {
        $this->customerMeasurementRepository = $customerMeasurementRepository;
        $this->customerMeasurementProvider = $customerMeasurementProvider;
        $this->customerMeasurementItemProvider = $customerMeasurementItemProvider;
        $this->measurementTypeProvider = $measurementTypeProvider;
        $this->customerMeasurementItemRepository = $customerMeasurementItemRepository;
        $this->customerManager = $customerManager;
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
            $takenAt = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $customerMeasurementRequest->takenAt);

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
            $measurementType = null;

            if (null !== $customerMeasurementItemRequest->type) {
                $measurementType = $this->measurementTypeProvider->getBySlug(
                    $customerMeasurementItemRequest->type
                );
            }

            if (null !== $customerMeasurementItemRequest->measurementTypeId) {
                $measurementType = $this->measurementTypeProvider->get(
                    Uuid::fromString($customerMeasurementItemRequest->measurementTypeId)
                );
            }

            if (null === $measurementType) {
                throw new MeasurementTypeNotFoundException();
            }

            $customerMeasurementItem = $this->customerMeasurementItemProvider->findOneByCustomerMeasurementAndType(
                $customerMeasurement, $measurementType
            );

            if (null === $customerMeasurementItem) {
                $customerMeasurementItem = new CustomerMeasurementItem();
                $customerMeasurementItem->setMeasurementType($measurementType);
            }

            $customerMeasurementItem->setMeasurement(new Decimal($customerMeasurementItemRequest->measurement));

            if (!$measurementType->isUnitValid($customerMeasurementItemRequest->unit)) {
                throw new CustomerMeasurementItemInvalidUnitException();
            }

            $customerMeasurementItem->setUnit($customerMeasurementItemRequest->unit);

            $customerMeasurement->addItem($customerMeasurementItem);

            if ('weight' === $measurementType->getSlug()) {
                $customer = $customerMeasurement->getCustomer();
                $customer->setLastWeight($customerMeasurementItem->getMeasurement());

                $this->customerManager->update($customer);
            }
        }
    }
}
