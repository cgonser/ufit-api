<?php

namespace App\Subscription\MessageHandler;

use App\Subscription\Message\SubscriptionApprovedEvent;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SubscriptionApprovedHandler implements MessageHandlerInterface
{
    private SubscriptionProvider $subscriptionProvider;

    private SubscriptionEmailManager $subscriptionEmailManager;

    private LoggerInterface $logger;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        SubscriptionEmailManager $subscriptionEmailManager,
        LoggerInterface $logger
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionEmailManager = $subscriptionEmailManager;
        $this->logger = $logger;
    }

    public function __invoke(SubscriptionApprovedEvent $subscriptionApprovedEvent)
    {
        $subscription = $this->subscriptionProvider->getById($subscriptionApprovedEvent->getSubscriptionId());

        $this->logger->info(
            'subscription.approved',
            [
                'id' => $subscription->getId()->toString(),
                'customerId' => $subscription->getCustomer()->getId()->toString(),
                'vendorPlanId' => $subscription->getVendorPlan()->getId()->toString(),
            ]
        );

        $this->subscriptionEmailManager->sendApprovedEmail($subscription);
    }
}
