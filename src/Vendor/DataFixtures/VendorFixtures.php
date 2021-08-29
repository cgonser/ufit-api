<?php

declare(strict_types=1);

namespace App\Vendor\DataFixtures;

use App\Vendor\Request\VendorRequest;
use App\Vendor\Service\VendorRequestManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Iterator;

class VendorFixtures extends Fixture
{
    /**
     * @var int
     */
    public const VENDOR_COUNT = 20;

    /**
     * @var string
     */
    public const VENDOR_DEFAULT_PASSWORD = '123';

    /**
     * @var string[]
     */
    public const COUNTRIES = ['BR', 'LU'];

    /**
     * @var array<string, string>
     */
    public const LOCALES = [
        'BR' => 'pt_BR',
        'LU' => 'en',
    ];

    /**
     * @var array<string, string>
     */
    public const TIMEZONES = [
        'BR' => 'America/Sao_Paulo',
        'LU' => 'Europe/Luxembourg',
    ];

    public function __construct(
        private VendorRequestManager $vendorRequestManager
    ) {
    }

    public function load(ObjectManager $objectManager): void
    {
        foreach ($this->getData() as $vendorRequest) {
            $vendor = $this->vendorRequestManager->createFromRequest($vendorRequest);

            $this->addReference('vendor-'.$vendor->getEmail(), $vendor);
        }

        $objectManager->flush();
    }

    /**
     * @return Iterator<VendorRequest>
     */
    private function getData(): Iterator
    {
        for ($i = 1; $i <= self::VENDOR_COUNT; ++$i) {
            $country = self::COUNTRIES[array_rand(self::COUNTRIES)];
            $locale = self::LOCALES[$country];
            $timezone = self::TIMEZONES[$country];
            $biography = 'É claro que o desenvolvimento contínuo de distintas formas de atuação é uma das consequências da '.
                'gestão inovadora da qual fazemos parte.\n\nTodas estas questões, devidamente ponderadas, levantam dúvidas '.
                'sobre se o desafiador cenário globalizado promove a alavancagem das condições financeiras e '.
                'administrativas exigidas.';

            $vendorRequest = new VendorRequest();
            $vendorRequest->name = 'Vendor '.$i;
            $vendorRequest->email = 'vendor'.$i.'@ufit.io';
            $vendorRequest->password = self::VENDOR_DEFAULT_PASSWORD;
            $vendorRequest->country = $country;
            $vendorRequest->locale = $locale;
            $vendorRequest->timezone = $timezone;
            $vendorRequest->socialLinks = [
                'facebook' => 'https://www.facebook.com/vampeta.eterno',
                'instagram' => 'https://www.instagram.com/marshalkimjongun/',
            ];
            $vendorRequest->biography = $biography;
            $vendorRequest->allowEmailMarketing = (bool) random_int(0, 1);

            yield $vendorRequest;
        }
    }
}
