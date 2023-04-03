<?php

declare(strict_types=1);

namespace Owl\Tests\Fixtures;

use Doctrine\ORM\QueryBuilder;
use Owl\Article\Application\List\ArticleCollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Type\AbstractCollectionType;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

final class DummyDataProvider extends AbstractCollectionType implements BuildableQueryBuilderInterface, ArticleCollectionDataProviderInterface
{
    private array $queryBuilderConfig;

    private array $filterConfig;

    private array $paginationConfig;

    private array $sortConfig;

    public function __construct(array $config = [])
    {
        $this->queryBuilderConfig = $config['query_builder'] ?? [];
        $this->filterConfig = $config['filters'] ?? [];
        $this->paginationConfig = $config['pagination'] ?? [];
        $this->sortConfig = $config['sort'] ?? [];
    }

    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {
        if (isset($this->queryBuilderConfig['with_add_select'])) {
            $queryBuilder->addSelect($this->queryBuilderConfig['with_add_select']);
        }
    }

    public function buildFilters(FilterBuilderInterface $filterBuilder): void
    {
        if ($this->filterConfig) {
            foreach ($this->filterConfig as $filter) {
                $filterBuilder
                    ->add($filter['name'], $filter['filter'], $filter['fields'])
                ;
            }
        }
    }

    public function buildPagination(PaginationBuilderInterface $paginationBuilder): void
    {
        if (isset($this->paginationConfig['hasPagination'])) {
            $paginationBuilder->setHasPagination($this->paginationConfig['hasPagination']);
        }
    }

    public function buildSort(SortBuilderInterface $sortBuilder): void
    {
        $available = $this->sortConfig['available'] ?? [];

        $sortBuilder
            ->setAvailable($available)
        ;
    }
}
