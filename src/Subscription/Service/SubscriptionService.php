<?php

namespace App\Subscription\Service;

use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Entity\Subscription;
use App\Subscription\Repository\SubscriptionRepository;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;

class SubscriptionService
{
    private CustomerProvider $customerProvider;

    private VendorPlanProvider $vendorPlanProvider;

    private SubscriptionRepository $subscriptionRepository;

    public function __construct(
        CustomerProvider $customerProvider,
        VendorPlanProvider $vendorPlanProvider,
        SubscriptionRepository $subscriptionRepository
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->customerProvider = $customerProvider;
        $this->vendorPlanProvider = $vendorPlanProvider;
    }

    public function createFromCustomerRequest(Customer $customer, SubscriptionRequest $subscriptionRequest): Subscription
    {
        $vendorPlan = $this->vendorPlanProvider->get(Uuid::fromString($subscriptionRequest->vendorPlanId));

        $subscription = (new Subscription())
            ->setCustomer($customer)
            ->setVendorPlan($vendorPlan)
        ;

        $this->subscriptionRepository->save($subscription);

        if (!$vendorPlan->isApprovalRequired()) {
            $this->approve($subscription);
        }

        return $subscription;
    }

    public function review(Subscription $subscription, SubscriptionReviewRequest $subscriptionReviewRequest)
    {
        if (true === $subscriptionReviewRequest->isApproved) {
            $this->approve($subscription);
        } else {
            $this->reject($subscription);
        }

        $subscription->setReviewNotes($subscriptionReviewRequest->reviewNotes);
        $subscription->setReviewedAt(new \DateTime());

        $this->subscriptionRepository->save($subscription);
    }

    public function reject(Subscription $subscription)
    {
        $subscription->setIsApproved(false);
        $subscription->setExpiresAt(new \DateTime());
    }

    public function approve(Subscription $subscription, ?string $reviewNotes = null)
    {
        $subscription->setIsApproved(true);

        $this->calculateExpiration($subscription);
    }

    private function calculateExpiration(Subscription $subscription)
    {
        $subscription->setExpiresAt(
            (new \DateTime())->add($subscription->getVendorPlan()->getDuration())
        );
    }

    public function delete(Subscription $subscription)
    {
        $this->subscriptionRepository->delete($subscription);
    }
}
