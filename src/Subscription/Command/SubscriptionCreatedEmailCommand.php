<?php

declare(strict_types=1);

namespace App\Subscription\Command;

use App\Subscription\Provider\SubscriptionProvider;
use App\Subscription\Service\SubscriptionEmailManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SubscriptionCreatedEmailCommand extends Command
{
    protected static $defaultName = 'ufit:subscription:created-email';

    private SubscriptionProvider $subscriptionProvider;

    private SubscriptionEmailManager $subscriptionEmailManager;

    public function __construct(
        SubscriptionProvider $subscriptionProvider,
        SubscriptionEmailManager $subscriptionEmailManager
    ) {
        $this->subscriptionProvider = $subscriptionProvider;
        $this->subscriptionEmailManager = $subscriptionEmailManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('subscriptionId')
            ->setDescription('Sends subscription created email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $subscription = $this->subscriptionProvider->get(Uuid::fromString($input->getArgument('subscriptionId')));

        $this->subscriptionEmailManager->sendCreatedEmail($subscription);

        return 0;
    }
}
