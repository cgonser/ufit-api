<?php

declare(strict_types=1);

namespace App\Subscription\MessageHandler;

use App\Subscription\Message\SubscriptionCreatedEvent;
use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SubscriptionCreatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private SubscriptionProvider $subscriptionProvider,
        private SubscriptionEmailManager $subscriptionEmailManager,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(SubscriptionCreatedEvent $subscriptionCreatedEvent)
    {
        $subscription = $this->subscriptionProvider->get($subscriptionCreatedEvent->getSubscriptionId());

        $this->logger->info(
            'subscription.created',
            [
                'id' => $subscription->getId()
                    ->toString(),
                'customerId' => $subscription->getCustomer()
                    ->getId()
                    ->toString(),
                'vendorPlanId' => $subscription->getVendorPlan()
                    ->getId()
                    ->toString(),
            ]
        );

        $this->subscriptionEmailManager->sendCreatedEmail($subscription);
    }
}
