<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;

interface ItemResultableApplicatorInterface extends CollectionApplicatorInterface
{
    public function supportsResult(ItemTypeInterface $collectionType, RequestParamsInterface $collectionRequestParams): bool;

    public function getResult(QueryBuilder $queryBuilder, ItemTypeInterface $collectionType, RequestParamsInterface $collectionRequestParams): ?object;
}
