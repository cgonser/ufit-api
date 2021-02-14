<?php

namespace App\Vendor\Command;

use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorEmailManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VendorCreatedEmailCommand extends Command
{
    protected static $defaultName = 'ufit:vendor:created-email';

    private VendorProvider $vendorProvider;

    private VendorEmailManager $vendorEmailManager;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorEmailManager $vendorEmailManager
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorEmailManager = $vendorEmailManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('vendorId')
            ->setDescription('Sends vendor created email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendor = $this->vendorProvider->get(
            Uuid::fromString($input->getArgument('vendorId'))
        );

        $this->vendorEmailManager->sendCreatedEmail($vendor);

        return 0;
    }
}
