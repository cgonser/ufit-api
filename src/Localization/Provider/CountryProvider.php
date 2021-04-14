<?php

namespace App\Localization\Provider;

use App\Core\Provider\AbstractProvider;
use App\Localization\Entity\Country;
use App\Localization\Repository\CountryRepository;

class CountryProvider extends AbstractProvider
{
    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function findOneByCode(string $countryCode): ?Country
    {
        return $this->repository->findOneBy(['code' => $countryCode]);
    }

    public function getByCode(string $countryCode): Country
    {
        $country = $this->findOneByCode($countryCode);

        if (!$country) {
            $this->throwNotFoundException();
        }

        return $country;
    }

    protected function getSearchableFields(): array
    {
        return [
            'name' => 'text',
        ];
    }

    protected function getFilterableFields(): array
    {
        return [
            'code',
            'customersEnabled',
            'vendorsEnabled',
        ];
    }
}
