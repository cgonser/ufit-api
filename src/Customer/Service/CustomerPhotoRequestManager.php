<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerPhoto;
use App\Customer\Exception\CustomerPhotoInvalidTakenAtException;
use App\Customer\Request\CustomerPhotoRequest;

class CustomerPhotoRequestManager
{
    private CustomerPhotoManager $customerPhotoManager;

    public function __construct(CustomerPhotoManager $customerPhotoManager)
    {
        $this->customerPhotoManager = $customerPhotoManager;
    }

    public function create(Customer $customer, CustomerPhotoRequest $customerPhotoRequest): CustomerPhoto
    {
        $customerPhoto = new CustomerPhoto();
        $customerPhoto->setCustomer($customer);

        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoManager->save($customerPhoto);

        return $customerPhoto;
    }

    public function update(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest): void
    {
        $this->mapFromRequest($customerPhoto, $customerPhotoRequest);

        $this->customerPhotoManager->save($customerPhoto);
    }

    public function uploadPhoto(CustomerPhoto $customerPhoto, string $photoContents): void
    {
        $this->customerPhotoManager->persistPhoto($customerPhoto, $photoContents);
    }

    private function mapFromRequest(CustomerPhoto $customerPhoto, CustomerPhotoRequest $customerPhotoRequest): void
    {
        $customerPhoto->setTitle($customerPhotoRequest->title);
        $customerPhoto->setDescription($customerPhotoRequest->description);

        if (null !== $customerPhotoRequest->takenAt) {
            $takenAt = \DateTime::createFromFormat(\DateTimeInterface::ATOM, $customerPhotoRequest->takenAt);

            if (false === $takenAt) {
                throw new CustomerPhotoInvalidTakenAtException();
            }

            $customerPhoto->setTakenAt($takenAt);
        } elseif (null === $customerPhoto->getTakenAt()) {
            $customerPhoto->setTakenAt(new \DateTime());
        }
    }
}
