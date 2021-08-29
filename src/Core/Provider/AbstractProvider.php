<?php

declare(strict_types=1);

namespace App\Core\Provider;

use App\Core\Exception\ResourceNotFoundException;
use App\Core\Request\SearchRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractProvider
{
    public const RESULTS_PER_PAGE = 10;

    protected ServiceEntityRepository $repository;

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function find(UuidInterface $id): ?object
    {
        return $this->repository->find($id);
    }

    public function get(UuidInterface $id): object
    {
        /** @var object|null $object */
        $object = $this->repository->find($id);

        if (null === $object) {
            $this->throwNotFoundException();
        }

        return $object;
    }

    public function getBy(array $criteria): object
    {
        /** @var object|null $object */
        $object = $this->repository->findOneBy($criteria);

        if (! $object) {
            $this->throwNotFoundException();
        }

        return $object;
    }

    public function search(SearchRequest $searchRequest, ?array $filters = null): array
    {
        $orderExpression = 'root.'.($searchRequest->orderProperty ?: 'createdAt');
        $orderDirection = $searchRequest->orderDirection ?: 'DESC';

        $limit = $searchRequest->resultsPerPage ?: self::RESULTS_PER_PAGE;
        $offset = ($searchRequest->page - 1) * $limit;

        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters)
            ->orderBy($orderExpression, $orderDirection)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        return $queryBuilder->getQuery()
            ->useQueryCache(true)
            ->getResult();
    }

    public function count(SearchRequest $searchRequest, ?array $filters = null): int
    {
        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters);

        return (int) $queryBuilder->select('COUNT(root.id)')
            ->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }

    protected function buildQueryBuilder(): QueryBuilder
    {
        return $this->repository->createQueryBuilder('root');
    }

    protected function buildSearchQueryBuilder(SearchRequest $searchRequest, ?array $filters = null): QueryBuilder
    {
        $queryBuilder = $this->buildQueryBuilder();

        if (null !== $searchRequest->search) {
            $this->addSearchClause($queryBuilder, $searchRequest->search);
        }

        if (null === $filters) {
            $filters = $this->prepareFilters($searchRequest);
        }

        if (! empty($filters)) {
            $this->addFilters($queryBuilder, $filters);
        }

        return $queryBuilder;
    }

    protected function prepareFilters(SearchRequest $searchRequest): array
    {
        $filters = [];

        foreach ($this->getFilterableFields() as $fieldName) {
            if (is_array($fieldName)) {
                $property = array_key_first($fieldName);
                $entity = $fieldName[$property];
            } else {
                $property = $fieldName;
                $entity = 'root';
            }

            if (! property_exists($searchRequest, $property)) {
                continue;
            }

            if (null !== $searchRequest->{$property}) {
                $filters[$entity.'.'.$property] = $searchRequest->{$property};
            }
        }

        return $filters;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters)
    {
        $i = 0;

        foreach ($filters as $fieldName => $value) {
            ++$i;

            $queryBuilder->andWhere(sprintf('%s = :filter_'.md5($fieldName), $fieldName))
                ->setParameter('filter_'.md5($fieldName), $value);
        }
    }

    protected function addSearchClause(QueryBuilder $queryBuilder, ?string $search)
    {
        if (null === $search || 0 === strlen(trim($search)) || empty($this->getSearchableFields())) {
            return;
        }

        $searchFields = [];

        foreach ($this->getSearchableFields() as $fieldName => $fieldType) {
            if ('text' === $fieldType) {
                $searchFields[] = $queryBuilder->expr()->like(sprintf('LOWER(root.%s)', $fieldName), ':searchText');
            }
        }

        $queryBuilder
            ->andWhere($queryBuilder->expr()->orX(...$searchFields))
            ->setParameter('searchText', '%'.strtolower($search).'%');
    }

    protected function getSearchableFields(): array
    {
        return [];
    }

    protected function getFilterableFields(): array
    {
        return [];
    }

    protected function throwNotFoundException()
    {
        throw new ResourceNotFoundException();
    }
}
