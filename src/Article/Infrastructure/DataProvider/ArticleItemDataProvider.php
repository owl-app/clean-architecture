<?php

declare(strict_types=1);

namespace Owl\Article\Infrastructure\DataProvider;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\AbstractItemType;

final class ArticleItemDataProvider extends AbstractItemType
{
    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {
        $queryBuilder->select('partial o.{id,title, description}');
    }
}
