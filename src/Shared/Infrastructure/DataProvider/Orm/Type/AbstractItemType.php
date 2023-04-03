<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Type;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Type\AbstractItemType as DomainAbstractItemType;

abstract class AbstractItemType extends DomainAbstractItemType implements BuildableQueryBuilderInterface
{
    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {
    }
}
