<?php

namespace App\Localization\Service;

use App\Localization\Entity\Currency;
use App\Localization\Provider\CurrencyProvider;
use App\Localization\Repository\CurrencyRepository;
use App\Localization\Request\CurrencyRequest;

class CurrencyService
{
    private CurrencyRepository $currencyRepository;

    private CurrencyProvider $currencyProvider;

    public function __construct(
        CurrencyRepository $currencyRepository,
        CurrencyProvider $currencyProvider
    ) {
        $this->currencyRepository = $currencyRepository;
        $this->currencyProvider = $currencyProvider;
    }

    public function create(CurrencyRequest $currencyRequest): Currency
    {
        $currency = new Currency();

        $this->mapFromRequest($currency, $currencyRequest);

        $this->currencyRepository->save($currency);

        return $currency;
    }

    public function update(Currency $currency, CurrencyRequest $currencyRequest)
    {
        $this->mapFromRequest($currency, $currencyRequest);

        $this->currencyRepository->save($currency);
    }

    public function mapFromRequest(Currency $currency, CurrencyRequest $currencyRequest)
    {
        $existingCurrency = $this->currencyProvider->findOneByCode($currencyRequest->code);

        if ($existingCurrency &&
            ($currency->isNew() || $existingCurrency->getId()->toString() != $currency->getId()->toString())
        ) {
            throw new CurrencyAlreadyExistsException();
        }

        $currency->setName($currencyRequest->name);
        $currency->setCode($currencyRequest->code);
    }

    public function delete(Currency $currency)
    {
        $this->currencyRepository->delete($currency);
    }
}
