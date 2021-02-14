<?php

namespace App\Core\Provider;

use App\Core\Exception\ResourceNotFoundException;
use App\Core\Request\SearchRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractProvider
{
    const RESULTS_PER_PAGE = 10;

    protected ServiceEntityRepository $repository;

    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    public function get(UuidInterface $id): object
    {
        /** @var object|null $program */
        $object = $this->repository->find($id);

        if (null === $object) {
            $this->throwNotFoundException();
        }

        return $object;
    }

    public function search(SearchRequest $searchRequest, ?array $filters = []): array
    {
        $orderExpression = 'root.'.($searchRequest->orderProperty ?: 'id');
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

    public function count(SearchRequest $searchRequest, ?array $filters = []): int
    {
        $queryBuilder = $this->buildSearchQueryBuilder($searchRequest, $filters);

        return (int) $queryBuilder->select('COUNT(root.id)')
            ->getQuery()
            ->useQueryCache(true)
            ->getSingleScalarResult();
    }

    protected function buildSearchQueryBuilder(SearchRequest $searchRequest, ?array $filters = []): QueryBuilder
    {
        $queryBuilder = $this->repository->createQueryBuilder('root');

        if (null !== $searchRequest->search) {
            $this->addSearchClause($queryBuilder, $searchRequest->search);
        }

        if (!empty($filters)) {
            $this->addFilters($queryBuilder, $filters);
        }

        return $queryBuilder;
    }

    protected function addFilters(QueryBuilder $queryBuilder, array $filters)
    {
        $i = 0;

        foreach ($filters as $fieldName => $value) {
            ++$i;
            $queryBuilder->andWhere(sprintf('root.%s = :filter_'.$i, $fieldName))
                ->setParameter('filter_'.$i, $value);
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
                $searchFields[] = $queryBuilder->expr()->like(
                    sprintf('LOWER(root.%s)', $fieldName), ':searchText'
                );
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

    protected function throwNotFoundException()
    {
        throw new ResourceNotFoundException();
    }
}
