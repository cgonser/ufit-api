<?php

namespace App\Core\ResponseMapper;

use App\Core\Dto\CurrencyDto;
use App\Core\Entity\Currency;

class CurrencyResponseMapper
{
    public function map(Currency $currency): CurrencyDto
    {
        $currencyDto = new CurrencyDto();
        $currencyDto->name = $currency->getName();
        $currencyDto->code = $currency->getCode();

        return $currencyDto;
    }

    public function mapMultiple(array $currencies): array
    {
        $currencyDtos = [];

        foreach ($currencies as $currency) {
            $currencyDtos[] = $this->map($currency);
        }

        return $currencyDtos;
    }
}