<?php

namespace App\Vendor\MessageHandler;

use App\Vendor\Message\VendorCreatedEvent;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class VendorCreatedHandler implements MessageHandlerInterface
{
    private VendorProvider $vendorProvider;

    private VendorEmailManager $vendorEmailManager;

    private LoggerInterface $logger;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorEmailManager $vendorEmailManager,
        LoggerInterface $logger
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorEmailManager = $vendorEmailManager;
        $this->logger = $logger;
    }

    public function __invoke(VendorCreatedEvent $vendorCreatedEvent)
    {
        $vendor = $this->vendorProvider->get($vendorCreatedEvent->getVendorId());

        $this->logger->info(
            'vendor.created',
            [
                'id' => $vendor->getId()->toString(),
            ]
        );

        $this->vendorEmailManager->sendCreatedEmail($vendor);
    }
}
