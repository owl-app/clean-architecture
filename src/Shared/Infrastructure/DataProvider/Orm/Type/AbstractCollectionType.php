<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Type;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Type\AbstractCollectionType as DomainAbstractCollectionType;

abstract class AbstractCollectionType extends DomainAbstractCollectionType implements BuildableQueryBuilderInterface
{
    public function buildQueryBuilder(QueryBuilder $queryBuilder): void
    {
    }
}
