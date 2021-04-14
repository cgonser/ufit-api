<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerGoogleLoginFailedException;
use App\Customer\Provider\CustomerProvider;
use Google_Client;

class CustomerLoginGoogleManager
{
    private Google_Client $googleClient;

    private CustomerProvider $customerProvider;

    private CustomerManager $customerManager;

    public function __construct(
        Google_Client $googleClient,
        CustomerProvider $customerProvider,
        CustomerManager $customerManager
    ) {
        $this->googleClient = $googleClient;
        $this->customerProvider = $customerProvider;
        $this->customerManager = $customerManager;
    }

    public function prepareCustomerFromGoogleToken(string $accessToken): Customer
    {
        try {
            $payload = $this->googleClient->verifyIdToken($accessToken);

            if (!$payload || !isset($payload['email'])) {
                throw new CustomerGoogleLoginFailedException();
            }

            $customer = $this->customerProvider->findOneByEmail($payload['email']);

            if (!$customer) {
                $customer = $this->createCustomerFromPayload($payload);
            }

            return $customer;
        } catch (\Exception $e) {
            throw new CustomerGoogleLoginFailedException();
        }
    }

    private function createCustomerFromPayload(array $payload): Customer
    {
        $customer = new Customer();
        $customer->setName($payload['name']);
        $customer->setLocale($payload['locale']);
        $customer->setEmail($payload['email']);

        $this->customerManager->create($customer);

        return $customer;
    }
}
