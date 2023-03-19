<?php

declare(strict_types=1);

namespace Owl\Tests\Fixtures;

use Doctrine\ORM\QueryBuilder;
use Owl\Article\Application\List\ArticleCollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Type\AbstractCollectionType;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

final class DummyEmptyDataProvider extends AbstractCollectionType implements BuildableQueryBuilderInterface, ArticleCollectionDataProviderInterface
{
    public function buildFilters(FilterBuilderInterface $filterBuilder): void
    {

    }

    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {

    }

    public function buildSort(SortBuilderInterface $sortBuilder): void
    {
        $sortBuilder
            ->setParamName('sort')
            ->setAvailable(['id'])
        ;
    }
}
