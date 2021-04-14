<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerFacebookLoginFailedException;
use App\Customer\Provider\CustomerProvider;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;

class CustomerFacebookLoginManager
{
    private Facebook $facebook;

    private CustomerProvider $customerProvider;

    private CustomerManager $customerManager;

    public function __construct(
        Facebook $facebook,
        CustomerProvider $customerProvider,
        CustomerManager $customerManager
    ) {
        $this->facebook = $facebook;
        $this->customerProvider = $customerProvider;
        $this->customerManager = $customerManager;
    }

    public function prepareCustomerFromFacebookToken(string $accessToken): Customer
    {
        try {
            $response = $this->facebook->get('/me?fields=id,name,email,picture', $accessToken);

            $graphUser = $response->getGraphUser();

            return $this->createOrUpdateCustomerFromGraphUser($graphUser);
        } catch (FacebookResponseException | FacebookSDKException $e) {
            throw new CustomerFacebookLoginFailedException();
        }
    }

    private function createOrUpdateCustomerFromGraphUser(GraphUser $graphUser): Customer
    {
        $customer = $this->customerProvider->findOneByEmail($graphUser->getEmail());

        if (!$customer) {
            $customer = new Customer();
            $customer->setName($graphUser->getName());
            $customer->setEmail($graphUser->getEmail());

            $this->customerManager->create($customer);
        }

        return $customer;
    }
}
