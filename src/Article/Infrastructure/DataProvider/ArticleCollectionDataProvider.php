<?php

declare(strict_types=1);

namespace Owl\Article\Infrastructure\DataProvider;

use Owl\Article\Application\List\ArticleCollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Filter\StringFilter;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\AbstractCollectionType;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

final class ArticleCollectionDataProvider extends AbstractCollectionType implements BuildableQueryBuilderInterface, ArticleCollectionDataProviderInterface
{
    public function buildFilters(FilterBuilderInterface $filterBuilder): void
    {
        $filterBuilder
            ->add('search', StringFilter::class, ['title', 'description'])
        ;
    }

    public function buildSort(SortBuilderInterface $sortBuilder): void
    {
        $sortBuilder
            ->setParamName('sort')
            ->setAvailable(['id', 'title'])
        ;
    }
}
