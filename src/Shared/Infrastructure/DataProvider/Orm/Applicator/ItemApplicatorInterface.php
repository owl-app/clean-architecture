<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;

interface ItemApplicatorInterface
{
    public function applyToItem(QueryBuilder $queryBuilder, ?ItemTypeInterface $itemType, RequestParamsInterface $requestParams, string $dataClass): void;
}
