<?php

declare(strict_types=1);

namespace App\Customer\Service;

use App\Core\Service\FacebookApiClientFactory;
use App\Customer\Entity\Customer;
use App\Customer\Entity\CustomerSocialNetwork;
use App\Customer\Exception\CustomerFacebookLoginFailedException;
use App\Customer\Provider\CustomerSocialNetworkProvider;
use App\Customer\Request\CustomerRequest;

class CustomerFacebookLoginManager
{
    public function __construct(
        private FacebookApiClientFactory $facebookApiClientFactory,
        private CustomerRequestManager $customerRequestManager,
        private CustomerSocialNetworkProvider $customerSocialNetworkProvider,
        private CustomerSocialNetworkManager $customerSocialNetworkManager
    ) {
    }

    public function prepareCustomerFromFacebookToken(string $accessToken, ?string $ipAddress = null): Customer
    {
        try {
            $facebookApi = $this->facebookApiClientFactory->createInstance($accessToken);
            $response = $facebookApi->call('/me?fields=id,name,email,picture');

            $graphUser = $response->getContent();
            $customer = $this->createOrUpdateCustomerFromGraphUser($graphUser, $ipAddress);
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

        if (null === $customerSocialNetwork) {
            $customerSocialNetwork = new CustomerSocialNetwork();
            $customerSocialNetwork->setCustomer($customer);
            $customerSocialNetwork->setExternalId($graphUser['id']);
            $customerSocialNetwork->setPlatform(CustomerSocialNetwork::PLATFORM_FACEBOOK);
        }

        $customerSocialNetwork->setAccessToken($accessToken);
        $customerSocialNetwork->setDetails($graphUser);

        $this->customerSocialNetworkManager->save($customerSocialNetwork);
    }

    private function createOrUpdateCustomerFromGraphUser(array $graphUser, ?string $ipAddress = null): Customer
    {
        $customerSocialNetwork = $this->customerSocialNetworkProvider->findOneByExternalIdAndPlatform(
            $graphUser['id'],
            CustomerSocialNetwork::PLATFORM_FACEBOOK
        );

        if ($customerSocialNetwork) {
            return $customerSocialNetwork->getCustomer();
        }

        $customerRequest = new CustomerRequest();
        $customerRequest->name = $graphUser['name'];
        $customerRequest->email = $graphUser['email'];

        return $this->customerRequestManager->createFromRequest($customerRequest, $ipAddress);
    }
}
