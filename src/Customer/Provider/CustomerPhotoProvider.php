<?php

declare(strict_types=1);

namespace App\Customer\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerPhoto;
use App\Customer\Exception\CustomerPhotoNotFoundException;
use App\Customer\Repository\CustomerPhotoRepository;
use Ramsey\Uuid\UuidInterface;

class CustomerPhotoProvider
{
    private CustomerPhotoRepository $customerPhotoRepository;

    public function __construct(CustomerPhotoRepository $customerPhotoRepository)
    {
        $this->customerPhotoRepository = $customerPhotoRepository;
    }

    public function get(UuidInterface $customerPhotoId): CustomerPhoto
    {
        /** @var CustomerPhoto|null $customerPhoto */
        $customerPhoto = $this->customerPhotoRepository->find($customerPhotoId);

        if (! $customerPhoto) {
            throw new CustomerPhotoNotFoundException();
        }

        return $customerPhoto;
    }

    public function getByCustomerAndId(Customer $customer, UuidInterface $customerPhotoId): CustomerPhoto
    {
        /** @var CustomerPhoto|null $customerPhoto */
        $customerPhoto = $this->customerPhotoRepository->findOneBy([
            'id' => $customerPhotoId,
            'customer' => $customer,
        ]);

        if (! $customerPhoto) {
            throw new CustomerPhotoNotFoundException();
        }

        return $customerPhoto;
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->customerPhotoRepository->findBy([
            'customer' => $customer,
        ]);
    }
}
