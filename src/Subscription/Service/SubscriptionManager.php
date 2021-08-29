<?php

namespace App\Subscription\Service;

use App\Core\Validation\EntityValidator;
use App\Payment\Entity\Invoice;
use App\Payment\Exception\InvoiceNotFoundException;
use App\Payment\Provider\InvoiceProvider;
use App\Payment\Service\InvoiceManager;
use App\Subscription\Entity\Subscription;
use App\Subscription\Entity\SubscriptionCycle;
use App\Subscription\Message\SubscriptionApprovedEvent;
use App\Subscription\Message\SubscriptionCreatedEvent;
use App\Subscription\Message\SubscriptionRejectedEvent;
use App\Subscription\Repository\SubscriptionRepository;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepository;
    private InvoiceProvider $invoiceProvider;
    private InvoiceManager $invoiceManager;
    private EntityValidator $validator;
    private MessageBusInterface $messageBus;
    private SubscriptionCycleManager $subscriptionCycleManager;

    public function __construct(
        SubscriptionRepository $subscriptionRepository,
        SubscriptionCycleManager $subscriptionCycleManager,
        InvoiceProvider $invoiceProvider,
        InvoiceManager $invoiceManager,
        EntityValidator $validator,
        MessageBusInterface $messageBus
    ) {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->invoiceManager = $invoiceManager;
        $this->invoiceProvider = $invoiceProvider;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
        $this->subscriptionCycleManager = $subscriptionCycleManager;
    }

    public function create(Subscription $subscription)
    {
        $vendorPlan = $subscription->getVendorPlan();

        $subscription
            ->setIsRecurring($vendorPlan->isRecurring())
            ->setPrice($vendorPlan->getPrice());

        $this->validator->validate($subscription);

        $this->subscriptionRepository->save($subscription);

        $this->messageBus->dispatch(new SubscriptionCreatedEvent($subscription->getId()));

        if (1 === $vendorPlan->getPrice()->compareTo(0)) {
            $this->getOrCreateUnpaidInvoice($subscription->getId());
        }

        if (!$vendorPlan->isApprovalRequired() && $vendorPlan->getPrice()->equals(0)) {
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

    public function approve(Subscription $subscription, ?string $reviewNotes = null, ?Invoice $invoice = null)
    {
        if ($subscription->isApproved()) {
            $this->renew($subscription, $invoice);

            return;
        }

        $subscription->setIsActive(true);
        $subscription->setIsApproved(true);
        $subscription->setValidFrom(new \DateTime());
        $subscription->setReviewNotes($reviewNotes);
        $subscription->setReviewedAt(new \DateTime());
        $subscription->setExpiresAt($this->calculateExpiration($subscription));

        $this->subscriptionRepository->save($subscription);

        $this->createCycle($subscription, $subscription->getValidFrom(), $subscription->getExpiresAt(), $invoice);

        $this->messageBus->dispatch(new SubscriptionApprovedEvent($subscription->getId()));
    }

    public function renew(Subscription $subscription, ?Invoice $invoice = null): void
    {
        $previousExpiration = clone $subscription->getExpiresAt();

        $newExpiration = $this->calculateExpiration($subscription)->format(\DateTimeInterface::ATOM);
        $subscription->setExpiresAt(\DateTime::createFromFormat(\DateTimeInterface::ATOM, $newExpiration));
        // overcome stupid doctrine bug

        $subscription->setIsActive(true);
        $this->subscriptionRepository->save($subscription);

        $this->createCycle(
            $subscription,
            $previousExpiration ?: new \DateTime(),
            $subscription->getExpiresAt(),
            $invoice
        );

//        $this->messageBus->dispatch(new SubscriptionRenewedEvent($subscription->getId()));
    }

    private function calculateExpiration(Subscription $subscription): ?\DateTime
    {
        if (null === $subscription->getVendorPlan()->getDuration()) {
            return null;
        }

//        dump($subscription->getVendorPlan()->getDuration());
//        dump($subscription->getExpiresAt());
//        dd(($subscription->getExpiresAt() ?: new \DateTime())->add($subscription->getVendorPlan()->getDuration()));

        return ($subscription->getExpiresAt() ?: new \DateTime())->add($subscription->getVendorPlan()->getDuration());
    }

    private function cancel(Subscription $subscription)
    {
        $subscription->setCancelledAt(new \DateTime());
        $subscription->setIsActive(false);
        // todo: calculate expiration

        $this->subscriptionRepository->save($subscription);
    }

    public function expire(Subscription $subscription)
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

    public function getOrCreateUnpaidInvoice(UuidInterface $subscriptionId): Invoice
    {
        try {
            return $this->invoiceProvider->getSubscriptionNextDueInvoice($subscriptionId);
        } catch (InvoiceNotFoundException $e) {
            return $this->invoiceManager->createFromSubscription(
                $this->subscriptionRepository->find($subscriptionId)
            );
        }
    }

    public function defineExternalRefence(Subscription $subscription, ?string $externalReference): void
    {
        $subscription->setExternalReference($externalReference);

        $this->subscriptionRepository->save($subscription);
    }

    private function createCycle(
        Subscription $subscription,
        \DateTimeInterface $startsAt,
        ?\DateTimeInterface $endsAt = null,
        ?Invoice $invoice = null
    ): void {
        $subscriptionCycle = $this->subscriptionCycleManager->create($subscription, $startsAt, $endsAt);

        if (null !== $invoice) {
            $invoice->setSubscriptionCycle($subscriptionCycle);

            $this->invoiceManager->save($invoice);
        }
    }
}
