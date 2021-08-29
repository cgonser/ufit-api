<?php

declare(strict_types=1);

namespace App\Vendor\MessageHandler;

use App\Vendor\Message\VendorUpdatedEvent;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VendorUpdatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorEmailManager $vendorEmailManager,
        private LoggerInterface $logger
    ) {
    }

    public function __invoke(VendorUpdatedEvent $vendorUpdatedEvent)
    {
        $vendor = $this->vendorProvider->get($vendorUpdatedEvent->getVendorId());

        $this->logger->info('vendor.updated', [
            'id' => $vendor->getId()
                ->toString(),
        ]);

        if (null === $vendor->getWelcomeEmailSentAt() && ($vendor->getName() || $vendor->getDisplayName())) {
            $this->vendorEmailManager->sendCreatedEmail($vendor);
        }
    }
}
