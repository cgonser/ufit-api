<?php

namespace App\Localization\Service;

use App\Localization\Provider\CurrencyProvider;
use App\Localization\Entity\Country;
use App\Localization\Request\CountryRequest;
use Ramsey\Uuid\Uuid;

class CountryRequestManager
{
    private CountryManager $countryManager;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        CountryManager $countryManager,
        CurrencyProvider $currencyProvider
    ) {
        $this->countryManager = $countryManager;
        $this->currencyProvider = $currencyProvider;
    }

    public function createFromRequest(CountryRequest $countryRequest): Country
    {
        $country = new Country();

        $this->mapFromRequest($country, $countryRequest);

        $this->countryManager->create($country);

        return $country;
    }

    public function updateFromRequest(Country $country, CountryRequest $countryRequest): void
    {
        $this->mapFromRequest($country, $countryRequest);

        $this->countryManager->update($country);
    }

    private function mapFromRequest(Country $country, CountryRequest $countryRequest): void
    {
        if (null !== $countryRequest->currencyId) {
            $country->setCurrency(
                $this->currencyProvider->get(Uuid::fromString($countryRequest->currencyId))
            );
        }

        if (null !== $countryRequest->code) {
            $country->setCode($countryRequest->code);
        }

        if (null !== $countryRequest->name) {
            $country->setName($countryRequest->name);
        }

        if (null !== $countryRequest->primaryTimezone) {
            $country->setPrimaryTimezone($countryRequest->primaryTimezone);
        }

        if (null !== $countryRequest->primaryLocale) {
            $country->setPrimaryLocale($countryRequest->primaryLocale);
        }

        if (null !== $countryRequest->vendorsEnabled) {
            $country->setVendorsEnabled($countryRequest->vendorsEnabled);
        }

        if (null !== $countryRequest->customersEnabled) {
            $country->setCustomersEnabled($countryRequest->customersEnabled);
        }

        if (null !== $countryRequest->documentName) {
            $country->setDocumentName($countryRequest->documentName);
        }
    }
}
