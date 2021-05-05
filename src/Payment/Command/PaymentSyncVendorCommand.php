<?php

namespace App\Payment\Command;

use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentSyncVendorCommand extends Command
{
    protected static $defaultName = 'ufit:payment:sync-vendor';

    private VendorInformationManagerInterface $vendorInformationManager;

    public function __construct(
        VendorInformationManagerInterface $vendorInformationManager
    ) {
        $this->vendorInformationManager = $vendorInformationManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('vendorId')
            ->setDescription('Sync vendor information with the payment gateway')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->vendorInformationManager->updateVendorInformation(
            Uuid::fromString($input->getArgument('vendorId'))
        );

        return 0;
    }
}
