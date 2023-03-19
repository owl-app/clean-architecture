<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Filter;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Filter\FilterInterface as DomainFilterInterface;
use Owl\Shared\Domain\DataProvider\Util\QueryNameGeneratorInterface;

interface FilterInterface extends DomainFilterInterface
{
    public function buildQuery(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, mixed $data, array $fieldAliases, array $options): void;
}
