<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Resolver;

use Doctrine\ORM\QueryBuilder;

interface FieldResolverInterface
{
    public function resolveFieldByAddingJoins(QueryBuilder $queryBuilder, string $field): string;
}
