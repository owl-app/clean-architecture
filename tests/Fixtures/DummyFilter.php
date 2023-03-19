<?php

declare(strict_types=1);

namespace Owl\Tests\Fixtures;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Util\QueryNameGeneratorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Filter\AbstractFilter;

final class DummyFilter extends AbstractFilter
{
    public function buildQuery(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, mixed $data, array $fieldAliases, array $options): void
    {
        $queryBuilder->addSelect(current($fieldAliases));
    }
}
