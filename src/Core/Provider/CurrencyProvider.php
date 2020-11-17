<?php

namespace App\Core\Provider;

use App\Core\Entity\Currency;
use App\Core\Exception\CurrencyNotFoundException;
use App\Core\Repository\CurrencyRepository;
use Ramsey\Uuid\UuidInterface;

class CurrencyProvider
{
    private CurrencyRepository $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function get(UuidInterface $currencyId): Currency
    {
        /** @var Currency|null $currency */
        $currency = $this->currencyRepository->find($currencyId);

        if (!$currency) {
            throw new CurrencyNotFoundException();
        }

        return $currency;
    }

    public function getByCode(string $code): Currency
    {
        /** @var Currency|null $currency */
        $currency = $this->findOneByCode($code);

        if (!$currency) {
            throw new CurrencyNotFoundException();
        }

        return $currency;
    }

    public function findOneByCode(string $code): ?Currency
    {
        return $this->currencyRepository->findOneBy(['code' => $code]);
    }

    public function findAll(): array
    {
        return $this->currencyRepository->findAll();
    }
}