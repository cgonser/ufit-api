<?php

declare(strict_types=1);

namespace App\Subscription\Provider;

use App\Core\Provider\AbstractProvider;
use App\Core\Request\SearchRequest;
use App\Customer\Entity\Customer;
use App\Customer\Repository\CustomerRepository;
use App\Subscription\Entity\Subscription;
use App\Subscription\Exception\SubscriptionNotFoundException;
use App\Subscription\Repository\SubscriptionRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

class SubscriptionProvider extends AbstractProvider
{
    private CustomerRepository $customerRepository;

    public function __construct(SubscriptionRepository $repository, CustomerRepository $customerRepository)
    {
        $this->repository = $repository;
        $this->customerRepository = $customerRepository;
    }

    public function searchCustomers(SearchRequest $searchRequest, ?array $filters = null): array
    {
        $orderExpression = 'customer.'.($searchRequest->orderProperty ?: 'createdAt');
        $orderDirection = $searchRequest->orderDirection ?: 'DESC';

        $limit = $searchRequest->resultsPerPage ?: self::RESULTS_PER_PAGE;
        $offset = ($searchRequest->page - 1) * $limit;

        $queryBuilder = $this->buildCustomerSearchQueryBuilder($searchRequest, $filters)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy($orderExpression, $orderDirection);

        return $queryBuilder->getQuery()
            ->getResult();
    }

    public function countCustomers(SearchRequest $searchRequest, ?array $filters = null): int
    {
        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters);

        return (int) $queryBuilder->select('COUNT(DISTINCT root.customerId)')
            ->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }

    public function findByCustomer(Customer $customer): array
    {
        return $this->repository->findBy([
            'customer' => $customer,
        ]);
    }

    public function getByExternalReference(string $externalReference): Subscription
    {
        return $this->getBy([
            'externalReference' => $externalReference,
        ]);
    }

    public function getByCustomerAndId(Customer $customer, UuidInterface $subscriptionId): Subscription
    {
        return $this->getBy([
            'id' => $subscriptionId,
            'customer' => $customer,
        ]);
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return parent::buildQueryBuilder()
            ->innerJoin('root.vendorPlan', 'vendorPlan');
    }

    protected function buildCustomerSearchQueryBuilder(
        SearchRequest $searchRequest,
        ?array $filters = null
    ): QueryBuilder {
        $customerIdsQueryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters)
            ->select('DISTINCT root.customerId');

        $parameters = $customerIdsQueryBuilder->getParameters();

        $queryBuilder = $this->customerRepository->createQueryBuilder('customer');
        $queryBuilder->where($queryBuilder->expr()->in('customer.id', $customerIdsQueryBuilder->getDQL()));

        foreach ($parameters as $parameter) {
            $queryBuilder->setParameter($parameter->getName(), $parameter->getValue());
        }

        return $queryBuilder;
    }

    protected function throwNotFoundException()
    {
        throw new SubscriptionNotFoundException();
    }

    protected function getFilterableFields(): array
    {
        return [
            [
                'vendorId' => 'vendorPlan',
            ],
            'customerId',
            'vendorPlanId',
            'isActive',
            'isPending',
        ];
    }
}
