<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Type;

use Doctrine\ORM\QueryBuilder;

interface BuildableQueryBuilderInterface
{
    public function buildQueryBuilder(QueryBuilder $queryBuilder): void;
}
