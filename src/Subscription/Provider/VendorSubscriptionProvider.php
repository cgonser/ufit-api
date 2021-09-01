<?php

declare(strict_types=1);

namespace App\Subscription\Provider;

use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use App\Subscription\Request\SubscriptionSearchRequest;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\UuidInterface;

class VendorSubscriptionProvider
{
    public function __construct(private SubscriptionRepository $subscriptionRepository)
    {
    }

    public function findWithRequest(Vendor $vendor, SubscriptionSearchRequest $subscriptionSearchRequest): array
    {
        if (true === $subscriptionSearchRequest->isActive) {
            return $this->subscriptionRepository->findActiveByVendor($vendor);
        }

        if (false === $subscriptionSearchRequest->isActive) {
            return $this->subscriptionRepository->findInactiveByVendor($vendor);
        }

        if (true === $subscriptionSearchRequest->isPending) {
            return $this->subscriptionRepository->findPendingByVendor($vendor);
        }

        return $this->subscriptionRepository->findByVendor($vendor);
    }

    public function getByVendorAndId(Vendor $vendor, UuidInterface $subscriptionId): Subscription
    {
        $subscription = $this->subscriptionRepository->findOneByVendorAndId($vendor, $subscriptionId);

        if (!$subscription instanceof Subscription) {
            throw new SubscriptionNotFoundException();
        }

        return $subscription;
    }
}
