<?php

namespace App\Core\Provider;

use App\Core\Entity\Currency;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Repository\CurrencyRepository;

class CurrencyProvider
{
    private CurrencyRepository $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function getByCode(string $code): Currency
    {
        /** @var Currency|null $currency */
        $currency = $this->currencyRepository->findOneBy(['code' => $code]);

        if (!$currency) {
            throw new CurrencyNotFoundException();
        }

        return $currency;
    }

    public function findAll(): array
    {
        return $this->currencyRepository->findAll();
    }
}