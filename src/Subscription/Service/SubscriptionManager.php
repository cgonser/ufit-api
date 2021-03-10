<?php

namespace App\Subscription\Service;

use App\Core\Validation\EntityValidator;
use App\Payment\Service\InvoiceManager;
use App\Subscription\Entity\Subscription;
use App\Subscription\Message\SubscriptionApprovedEvent;
use App\Subscription\Message\SubscriptionCreatedEvent;
use App\Subscription\Message\SubscriptionRejectedEvent;
use App\Subscription\Repository\SubscriptionRepository;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepository;

    private InvoiceManager $invoiceManager;

    private EntityValidator $validator;

    private MessageBusInterface $messageBus;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        InvoiceManager $invoiceManager,
        EntityValidator $validator,
        MessageBusInterface $messageBus
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->messageBus = $messageBus;
        $this->validator = $validator;
        $this->invoiceManager = $invoiceManager;
    }

    public function create(Subscription $subscription)
    {
        $vendorPlan = $subscription->getVendorPlan();

        $subscription
            ->setIsRecurring($vendorPlan->isRecurring())
            ->setPrice($vendorPlan->getPrice())
        ;

        $this->validator->validate($subscription);

        $this->subscriptionRepository->save($subscription);

        $this->messageBus->dispatch(new SubscriptionCreatedEvent($subscription->getId()));

        if (1 === $vendorPlan->getPrice()->compareTo(0)) {
            $this->generateInvoice($subscription);
        }

        if ($vendorPlan->getPrice()->equals(0) && !$vendorPlan->isApprovalRequired()) {
            $this->approve($subscription);
        }
    }

    public function reject(Subscription $subscription, ?string $reviewNotes = null)
    {
        $subscription->setIsApproved(false);
        $subscription->setExpiresAt(new \DateTime());
        $subscription->setReviewNotes($reviewNotes);
        $subscription->setReviewedAt(new \DateTime());

        $this->subscriptionRepository->save($subscription);

        $this->messageBus->dispatch(new SubscriptionRejectedEvent($subscription->getId()));
    }

    public function approve(Subscription $subscription, ?string $reviewNotes = null)
    {
        $subscription->setIsApproved(true);
        $subscription->setValidFrom(new \DateTime());
        $subscription->setReviewNotes($reviewNotes);
        $subscription->setReviewedAt(new \DateTime());

        $this->calculateExpiration($subscription);

        $this->subscriptionRepository->save($subscription);

        $this->messageBus->dispatch(new SubscriptionApprovedEvent($subscription->getId()));
    }

    private function calculateExpiration(Subscription $subscription)
    {
        if ($subscription->getVendorPlan()->isRecurring() || null === $subscription->getVendorPlan()->getDuration()) {
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
        $subscription->setExpiresAt(new \DateTime());

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

    public function generateInvoice(Subscription $subscription)
    {
        $this->invoiceManager->createFromSubscription($subscription);
    }
}
