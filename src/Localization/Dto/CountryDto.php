<?php

namespace App\Localization\Dto;

class CountryDto
{
    public ?string $code;

    public ?string $name;

    public ?CurrencyDto $currency;

    public ?string $primaryLocale;

    public ?string $primaryTimezone;

    public ?array $timezones;

    public ?bool $vendorsEnabled;

    public ?bool $customersEnabled;
}
