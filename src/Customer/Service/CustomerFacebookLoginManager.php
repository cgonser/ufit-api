<?php

namespace App\Customer\Service;

use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerSocialNetwork;
use App\Customer\Exception\CustomerFacebookLoginFailedException;
use App\Customer\Provider\CustomerSocialNetworkProvider;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphUser;

class CustomerFacebookLoginManager
{
    private Facebook $facebook;

    private CustomerManager $customerManager;

    private CustomerSocialNetworkProvider $customerSocialNetworkProvider;

    private CustomerSocialNetworkManager $customerSocialNetworkManager;

    public function __construct(
        Facebook $facebook,
        CustomerManager $customerManager,
        CustomerSocialNetworkProvider $customerSocialNetworkProvider,
        CustomerSocialNetworkManager $customerSocialNetworkManager
    ) {
        $this->facebook = $facebook;
        $this->customerManager = $customerManager;
        $this->customerSocialNetworkProvider = $customerSocialNetworkProvider;
        $this->customerSocialNetworkManager = $customerSocialNetworkManager;
    }

    public function prepareCustomerFromFacebookToken(string $accessToken): Customer
    {
        try {
            $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);

            $graphUser = $response->getGraphUser();
            $customer = $this->createOrUpdateCustomerFromGraphUser($graphUser);
            $this->createOrUpdateCustomerSocialNetwork($customer, $graphUser, $accessToken);

            return $customer;
        } catch (FacebookResponseException | FacebookSDKException $e) {
            throw new CustomerFacebookLoginFailedException();
        }
    }

    private function createOrUpdateCustomerSocialNetwork(Customer $customer, array $graphUser, string $accessToken): void
    {
        $customerSocialNetwork = $this->customerSocialNetworkProvider->findOneByCustomerAndPlatform(
            $customer,
            CustomerSocialNetwork::PLATFORM_FACEBOOK
        );

        if (!$customerSocialNetwork) {
            $customerSocialNetwork = new CustomerSocialNetwork();
            $customerSocialNetwork->setCustomer($customer);
            $customerSocialNetwork->setExternalId($graphUser['id']);
            $customerSocialNetwork->setPlatform(CustomerSocialNetwork::PLATFORM_FACEBOOK);
        }

        $customerSocialNetwork->setAccessToken($accessToken);
        $customerSocialNetwork->setDetails($graphUser);

        $this->customerSocialNetworkManager->save($customerSocialNetwork);
    }

    private function createOrUpdateCustomerFromGraphUser(GraphUser $graphUser): Customer
    {
        $customerSocialNetwork = $this->customerSocialNetworkProvider->findOneByExternalIdAndPlatform(
            $graphUser->getId(),
            CustomerSocialNetwork::PLATFORM_FACEBOOK
        );

        if ($customerSocialNetwork) {
            return $customerSocialNetwork->getCustomer();
        }

        $customer = new Customer();
        $customer->setName($graphUser->getName());
        $customer->setEmail($graphUser->getEmail());

        $this->customerManager->create($customer);

        return $customer;
    }
}
