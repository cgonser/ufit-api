<?php

declare(strict_types=1);

namespace App\Program\Provider;

use App\Customer\Entity\Customer;
use App\Program\Entity\Program;
use App\Program\Request\CustomerProgramSearchRequest;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class CustomerProgramProvider extends ProgramProvider
{
    public function getByCustomerAndId(Customer $customer, UuidInterface $programId): ?Program
    {
        $queryBuilder = $this->repository->createQueryBuilder('p')
            ->innerJoin('p.assignments', 'a')
            ->where('a.customer = :customer')
            ->andWhere('p.id = :programId')
            ->setParameter('customer', $customer)
            ->setParameter('programId', $programId)
            ->setMaxResults(1);

        /** @var Program|null $program */
        $program = $queryBuilder->getQuery()->getOneOrNullResult();

        if ($program === null) {
            $this->throwNotFoundException();
        }

        return $program;
    }

    public function searchCustomerPrograms(
        Customer $customer,
        CustomerProgramSearchRequest $customerProgramSearchRequest
    ): array {
        return $this->search($customerProgramSearchRequest, [
            'customer' => $customer,
        ]);
    }

    public function countCustomerPrograms(
        Customer $customer,
        CustomerProgramSearchRequest $customerProgramSearchRequest
    ): int {
        return $this->count($customerProgramSearchRequest, [
            'customer' => $customer,
        ]);
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters): void
    {
        if (isset($filters['customer'])) {
            $queryBuilder
                ->innerJoin('root.assignments', 'a')
                ->andWhere('a.customer = :customer')
                ->setParameter('customer', $filters['customer']);

            unset($filters['customer']);
        }

        parent::addFilters($queryBuilder, $filters);
    }
}
