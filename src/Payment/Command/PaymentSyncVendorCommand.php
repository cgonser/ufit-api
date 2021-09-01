<?php

declare(strict_types=1);

namespace App\Payment\Command;

use App\Payment\Service\PaymentProcessor\VendorInformationManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentSyncVendorCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'ufit:payment:sync-vendor';

    public function __construct(private VendorInformationManagerInterface $vendorInformationManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('vendorId')
            ->setDescription('Sync vendor information with the payment gateway')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->vendorInformationManager->updateVendorInformation(Uuid::fromString($input->getArgument('vendorId')));

        return 0;
    }
}
