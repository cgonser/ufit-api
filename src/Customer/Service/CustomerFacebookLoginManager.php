<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Core\Service\FacebookApiClientFactory;
use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerSocialNetwork;
use App\Customer\Exception\CustomerFacebookLoginFailedException;
use App\Customer\Provider\CustomerSocialNetworkProvider;

class CustomerFacebookLoginManager
{
    private CustomerManager $customerManager;
    private CustomerSocialNetworkProvider $customerSocialNetworkProvider;
    private CustomerSocialNetworkManager $customerSocialNetworkManager;
    private FacebookApiClientFactory $facebookApiClientFactory;

    public function __construct(
        FacebookApiClientFactory $facebookApiClientFactory,
        CustomerManager $customerManager,
        CustomerSocialNetworkProvider $customerSocialNetworkProvider,
        CustomerSocialNetworkManager $customerSocialNetworkManager
    ) {
        $this->customerManager = $customerManager;
        $this->customerSocialNetworkProvider = $customerSocialNetworkProvider;
        $this->customerSocialNetworkManager = $customerSocialNetworkManager;
        $this->facebookApiClientFactory = $facebookApiClientFactory;
    }

    public function prepareCustomerFromFacebookToken(string $accessToken): Customer
    {
        try {
            $facebookApi = $this->facebookApiClientFactory->createInstance($accessToken);
            $response = $facebookApi->call('/me?fields=id,name,email,picture');

            $graphUser = $response->getContent();
            $customer = $this->createOrUpdateCustomerFromGraphUser($graphUser);
            $this->createOrUpdateCustomerSocialNetwork($customer, $graphUser, $accessToken);

            return $customer;
        } catch (\Exception $e) {
            throw new CustomerFacebookLoginFailedException();
        }
    }

    private function createOrUpdateCustomerSocialNetwork(
        Customer $customer,
        array $graphUser,
        string $accessToken
    ): void {
        $customerSocialNetwork = $this->customerSocialNetworkProvider->findOneByCustomerAndPlatform(
            $customer,
            CustomerSocialNetwork::PLATFORM_FACEBOOK
        );

        if (! $customerSocialNetwork) {
            $customerSocialNetwork = new CustomerSocialNetwork();
            $customerSocialNetwork->setCustomer($customer);
            $customerSocialNetwork->setExternalId($graphUser['id']);
            $customerSocialNetwork->setPlatform(CustomerSocialNetwork::PLATFORM_FACEBOOK);
        }

        $customerSocialNetwork->setAccessToken($accessToken);
        $customerSocialNetwork->setDetails($graphUser);

        $this->customerSocialNetworkManager->save($customerSocialNetwork);
    }

    private function createOrUpdateCustomerFromGraphUser(array $graphUser): Customer
    {
        $customerSocialNetwork = $this->customerSocialNetworkProvider->findOneByExternalIdAndPlatform(
            $graphUser['id'],
            CustomerSocialNetwork::PLATFORM_FACEBOOK
        );

        if ($customerSocialNetwork) {
            return $customerSocialNetwork->getCustomer();
        }

        $customer = new Customer();
        $customer->setName($graphUser['name']);
        $customer->setEmail($graphUser['email']);

        $this->customerManager->create($customer);

        return $customer;
    }
}
