<?php

declare(strict_types=1);

namespace App\Payment\Provider;

use App\Core\Provider\AbstractProvider;
use App\Payment\Exception\PaymentMethodNotFoundException;
use App\Payment\Repository\PaymentMethodRepository;
use Doctrine\ORM\QueryBuilder;

class PaymentMethodProvider extends AbstractProvider
{
    public function __construct(PaymentMethodRepository $paymentMethodRepository)
    {
        $this->repository = $paymentMethodRepository;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
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

    protected function throwNotFoundException(): void
    {
        throw new PaymentMethodNotFoundException();
    }

    /**
     * @return array<string, string>
     */
    protected function getSearchableFields(): array
    {
        return [
            'name' => 'text',
        ];
    }

    /**
     * @return string[]
     */
    protected function getFilterableFields(): array
    {
        return ['countryCode'];
    }
}
