<?php

declare(strict_types=1);

namespace App\Vendor\Command;

use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorEmailManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VendorCreatedEmailCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'ufit:vendor:created-email';

    public function __construct(
        private VendorProvider $vendorProvider,
        private VendorEmailManager $vendorEmailManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('vendorId')
            ->setDescription('Sends vendor created email')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendor = $this->vendorProvider->get(Uuid::fromString($input->getArgument('vendorId')));

        $this->vendorEmailManager->sendCreatedEmail($vendor);

        return 0;
    }
}
