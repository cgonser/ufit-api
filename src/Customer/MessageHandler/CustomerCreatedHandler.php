<?php

namespace App\Customer\MessageHandler;

use App\Customer\Message\CustomerCreatedEvent;
use App\Customer\Provider\CustomerProvider;
use App\Customer\Service\CustomerEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CustomerCreatedHandler implements MessageHandlerInterface
{
    private CustomerProvider $customerProvider;

    private CustomerEmailManager $customerEmailManager;

    private LoggerInterface $logger;

    public function __construct(
        CustomerProvider $customerProvider,
        CustomerEmailManager $customerEmailManager,
        LoggerInterface $logger
    ) {
        $this->customerProvider = $customerProvider;
        $this->customerEmailManager = $customerEmailManager;
        $this->logger = $logger;
    }

    public function __invoke(CustomerCreatedEvent $customerCreatedEvent)
    {
        $customer = $this->customerProvider->get($customerCreatedEvent->getCustomerId());

        $this->logger->info(
            'customer.created',
            [
                'id' => $customer->getId()->toString(),
            ]
        );

        $this->customerEmailManager->sendCreatedEmail($customer);
    }
}
