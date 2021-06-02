<?php

namespace App\Localization\ResponseMapper;

use App\Localization\Dto\CountryDto;
use App\Localization\Entity\Country;

class CountryResponseMapper
{
    private CurrencyResponseMapper $currencyResponseMapper;

    public function __construct(CurrencyResponseMapper $currencyResponseMapper)
    {
        $this->currencyResponseMapper = $currencyResponseMapper;
    }

    public function map(Country $country): CountryDto
    {
        $countryDto = new CountryDto();
        $countryDto->code = $country->getCode();
        $countryDto->name = $country->getName();
        $countryDto->primaryLocale = $country->getPrimaryLocale();
        $countryDto->primaryTimezone = $country->getPrimaryTimezone();
        $countryDto->timezones = $country->getTimezones();
        $countryDto->vendorsEnabled = $country->isVendorsEnabled();
        $countryDto->customersEnabled = $country->isCustomersEnabled();
        $countryDto->documentName = $country->getDocumentName();

        if (null !== $country->getCurrency()) {
            $countryDto->currency = $this->currencyResponseMapper->map($country->getCurrency());
        }

        return $countryDto;
    }

    public function mapMultiple(array $countries): array
    {
        $countryDtos = [];

        foreach ($countries as $country) {
            $countryDtos[] = $this->map($country);
        }

        return $countryDtos;
    }
}