<?php

declare(strict_types=1);

namespace App\Vendor\Command;

use App\Vendor\Repository\VendorInstagramProfileRepository;
use App\Vendor\Service\VendorInstagramManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VendorInstagramProfileUpdateCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'ufit:vendor:instagram-update';

    public function __construct(
        private VendorInstagramProfileRepository $vendorInstagramProfileRepository,
        private VendorInstagramManager $vendorInstagramManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Updates instagram profile pictures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        new SymfonyStyle($input, $output);

        $vendorInstagramProfiles = $this->vendorInstagramProfileRepository->findAll();

        foreach ($vendorInstagramProfiles as $vendorInstagramProfile) {
            $this->vendorInstagramManager->updateVendorWithProfileData($vendorInstagramProfile);
        }

        return 0;
    }
}
