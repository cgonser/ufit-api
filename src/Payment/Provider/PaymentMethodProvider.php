<?php

namespace App\Payment\Provider;

use App\Core\Provider\AbstractProvider;
use App\Payment\Exception\PaymentMethodNotFoundException;
use App\Payment\Repository\PaymentMethodRepository;
use Doctrine\ORM\QueryBuilder;

class PaymentMethodProvider extends AbstractProvider
{
    public function __construct(PaymentMethodRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters)
    {
        if (isset($filters['root.countryCode'])) {
            $queryBuilder
                ->andWhere('JSONB_EXISTS(root.countriesEnabled, :countryCode) = TRUE')
                ->setParameter('countryCode', $filters['root.countryCode'])
            ;

            unset($filters['root.countryCode']);
        }

        parent::addFilters($queryBuilder, $filters);
    }

    protected function throwNotFoundException()
    {
        throw new PaymentMethodNotFoundException();
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
            'countryCode',
        ];
    }
}