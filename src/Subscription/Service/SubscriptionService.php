<?php

namespace App\Subscription\Service;

use App\Customer\Entity\Customer;
use App\Customer\Provider\CustomerProvider;
use App\Subscription\Entity\Subscription;
use App\Subscription\Message\SubscriptionCreatedEvent;
use App\Subscription\Repository\SubscriptionRepository;
use App\Subscription\Request\SubscriptionRequest;
use App\Subscription\Request\SubscriptionReviewRequest;
use App\Subscription\ResponseMapper\SubscriptionResponseMapper;
use App\Vendor\Provider\VendorPlanProvider;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionService
{
    private CustomerProvider $customerProvider;

    private VendorPlanProvider $vendorPlanProvider;

    private SubscriptionRepository $subscriptionRepository;

    private MessageBusInterface $messageBus;

    private SubscriptionResponseMapper $subscriptionResponseMapper;

    public function __construct(
        CustomerProvider $customerProvider,
        VendorPlanProvider $vendorPlanProvider,
        SubscriptionRepository $subscriptionRepository,
        SubscriptionResponseMapper $subscriptionResponseMapper,
        MessageBusInterface $messageBus
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->customerProvider = $customerProvider;
        $this->vendorPlanProvider = $vendorPlanProvider;
        $this->messageBus = $messageBus;
        $this->subscriptionResponseMapper = $subscriptionResponseMapper;
    }

    public function createFromCustomerRequest(Customer $customer, SubscriptionRequest $subscriptionRequest): Subscription
    {
        $vendorPlan = $this->vendorPlanProvider->get(Uuid::fromString($subscriptionRequest->vendorPlanId));

        $subscription = (new Subscription())
            ->setCustomer($customer)
            ->setVendorPlan($vendorPlan)
            ->setIsRecurring($vendorPlan->isRecurring())
            ->setPrice($vendorPlan->getPrice())
        ;

        $this->subscriptionRepository->save($subscription);

        if (!$vendorPlan->isApprovalRequired()) {
            $this->approve($subscription);
        }

        $this->messageBus->dispatch(new SubscriptionCreatedEvent($subscription->getId()));

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
        $subscription->setReviewNotes($reviewNotes);
        $subscription->setValidFrom(new \DateTime());

        $this->calculateExpiration($subscription);
    }

    private function calculateExpiration(Subscription $subscription)
    {
        if (null === $subscription->getVendorPlan()->getDuration()) {
            $subscription->setExpiresAt(null);

            return;
        }

        $subscription->setExpiresAt(
            (new \DateTime())->add($subscription->getVendorPlan()->getDuration())
        );
    }

    private function cancel(Subscription $subscription)
    {
        $subscription->setCancelledAt(new \DateTime());
        $subscription->setIsActive(false);

        $this->subscriptionRepository->save($subscription);
    }

    private function expire(Subscription $subscription)
    {
        $subscription->setIsActive(false);

        $this->subscriptionRepository->save($subscription);
    }

    public function customerCancellation(Subscription $subscription)
    {
        $subscription->setCancelledByCustomer(true);

        $this->cancel($subscription);
    }

    public function vendorCancellation(Subscription $subscription)
    {
        $subscription->setCancelledByVendor(true);

        $this->cancel($subscription);
    }
}
