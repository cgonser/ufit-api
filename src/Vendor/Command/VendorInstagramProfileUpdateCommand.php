<?php

namespace App\Vendor\Command;

use App\Vendor\Provider\VendorProvider;
use App\Vendor\Repository\VendorInstagramProfileRepository;
use App\Vendor\Service\VendorInstagramManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class VendorInstagramProfileUpdateCommand extends Command
{
    protected static $defaultName = 'ufit:vendor:instagram-update';

    private VendorProvider $vendorProvider;

    private VendorInstagramProfileRepository $vendorInstagramProfileRepository;

    private VendorInstagramManager $vendorInstagramManager;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorInstagramProfileRepository $vendorInstagramProfileRepository,
        VendorInstagramManager $vendorInstagramManager
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorInstagramProfileRepository = $vendorInstagramProfileRepository;
        $this->vendorInstagramManager = $vendorInstagramManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates instagram profile pictures')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $vendorInstagramProfiles = $this->vendorInstagramProfileRepository->findAll();

        foreach ($vendorInstagramProfiles as $vendorInstagramProfile) {
            $this->vendorInstagramManager->updateVendorWithProfileData($vendorInstagramProfile);
        }

        return 0;
    }
}