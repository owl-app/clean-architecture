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
    private array $filterConfig;

    private array $paginationConfig;

    public function __construct(array $config = [])
    {
        $this->filterConfig = $config['filter'] ?? [];
        $this->paginationConfig = $config['pagination'] ?? [];
    }

    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {

    }

    public function buildFilters(FilterBuilderInterface $filterBuilder): void
    {
        $filterBuilder
            ->add('filterName', DummyFilter::class, ['field1'])
        ;
    }

    public function buildPagination(PaginationBuilderInterface $paginationBuilder): void
    {
        if(isset($this->paginationConfig['hasPagination'])) {
            $paginationBuilder->setHasPagination($this->paginationConfig['hasPagination']);
        }
    }

    public function buildSort(SortBuilderInterface $sortBuilder): void
    {
        $sortBuilder
            ->setParamName('sort')
            ->setAvailable(['id'])
        ;
    }
}
