<?php

namespace App\Customer\Service;

use App\Core\Validation\EntityValidator;
use App\Customer\Entity\CustomerSocialNetwork;
use App\Customer\Repository\CustomerSocialNetworkRepository;

class CustomerSocialNetworkManager
{
    private CustomerSocialNetworkRepository $customerSocialNetworkRepository;

    private EntityValidator $validator;

    public function __construct(
        CustomerSocialNetworkRepository $customerSocialNetworkRepository,
        EntityValidator $validator
    ) {
        $this->customerSocialNetworkRepository = $customerSocialNetworkRepository;
        $this->validator = $validator;
    }

    public function save(CustomerSocialNetwork $customerSocialNetwork)
    {
        $this->validator->validate($customerSocialNetwork);

        $this->customerSocialNetworkRepository->save($customerSocialNetwork);
    }
}
