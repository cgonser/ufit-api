<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerPhoto;
use App\Customer\Exception\CustomerPhotoInvalidTakenAtException;
use App\Customer\Provider\CustomerPhotoProvider;
use App\Customer\Repository\CustomerPhotoRepository;
use App\Customer\Request\CustomerPhotoRequest;

class CustomerPhotoService
{
    private CustomerPhotoRepository $customerPhotoRepository;

    private CustomerPhotoProvider $customerPhotoProvider;

    public function __construct(
        CustomerPhotoRepository $customerPhotoRepository,
        CustomerPhotoProvider $customerPhotoProvider
    ) {
        $this->customerPhotoRepository = $customerPhotoRepository;
        $this->customerPhotoProvider = $customerPhotoProvider;
    }

    public function create(Customer $customer, CustomerPhotoRequest $customerPhotoRequest): CustomerPhoto
    {
        $customerPhoto = new CustomerPhoto();
        $customerPhoto->setCustomer($customer);

        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoRepository->save($customerPhoto);

        return $customerPhoto;
    }

    public function update(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest)
    {
        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoRepository->save($customerPhoto);
    }

    private function mapFromRequest(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest)
    {
        // TODO: photoType, filename
        $customerPhoto->setTitle($customerPhotoRequest->title);
        $customerPhoto->setDescription($customerPhotoRequest->description);

        if (null !== $customerPhotoRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat($customerPhotoRequest->takenAt, \DateTimeInterface::ISO8601);

            if (false === $takenAt) {
                throw new CustomerPhotoInvalidTakenAtException();
            }

            $customerPhoto->setTakenAt($takenAt);
        }
    }
}
