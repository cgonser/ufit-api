<?php

namespace App\Localization\Command;

use App\Localization\Service\CountryManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LocalizationDatabaseUpdateCommand extends Command
{
    protected static $defaultName = 'localization:database-update';

    private CountryManager $countryManager;

    public function __construct(
        CountryManager $countryManager
    ) {
        parent::__construct();

        $this->countryManager = $countryManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Updates app db with ICU data')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->countryManager->importCountries();

        return 0;
    }
}
