<?php

namespace App\Subscription\Provider;

use App\Customer\Entity\Customer;
use App\Customer\Exception\CustomerNotFoundException;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

class VendorSubscriptionProvider
{
    private SubscriptionRepository $subscriptionRepository;

    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function findWithRequest(
        Vendor $vendor,
        SubscriptionSearchRequest $subscriptionSearchRequest
    ): array {
        if (true === $subscriptionSearchRequest->isActive) {
            return $this->subscriptionRepository->findActiveByVendor($vendor);
        }

        if (true === $subscriptionSearchRequest->isInactive) {
            return $this->subscriptionRepository->findInactiveByVendor($vendor);
        }

        if (true === $subscriptionSearchRequest->isPending) {
            return $this->subscriptionRepository->findPendingByVendor($vendor);
        }

        return $this->subscriptionRepository->findByVendor($vendor);
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $subscriptionId): Subscription
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->subscriptionRepository->findOneByVendorAndId($vendor, $subscriptionId);

        if (!$subscription) {
            throw new SubscriptionNotFoundException();
        }

        return $subscription;
    }
}
