<?php

namespace App\Payment\Command;

use App\Vendor\Entity\Vendor;
use App\Vendor\Entity\VendorBankAccount;
use App\Vendor\Provider\VendorBankAccountProvider;
use App\Vendor\Provider\VendorProvider;
use App\Vendor\Service\VendorSettingManager;
use PagarMe\Client;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PaymentPagarmeSyncVendorCommand extends Command
{
    protected static $defaultName = 'ufit:pagarme:sync-vendor';

    private VendorProvider $vendorProvider;

    private VendorBankAccountProvider $vendorBankAccountProvider;

    private VendorSettingManager $vendorSettingManager;

    private Client $pagarmeClient;

    public function __construct(
        VendorProvider $vendorProvider,
        VendorBankAccountProvider $vendorBankAccountProvider,
        VendorSettingManager $vendorSettingManager,
        Client $pagarmeClient
    ) {
        $this->vendorProvider = $vendorProvider;
        $this->vendorBankAccountProvider = $vendorBankAccountProvider;
        $this->vendorSettingManager = $vendorSettingManager;
        $this->pagarmeClient = $pagarmeClient;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument('vendorId')
            ->setDescription('Payment test')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $vendorBankAccount = $this->vendorBankAccountProvider->getOneByVendorId(
            Uuid::fromString($input->getArgument('vendorId'))
        );

        $this->pushVendor($vendor, $vendorBankAccount);

        return 0;
    }
}
