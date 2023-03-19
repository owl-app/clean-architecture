<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

interface CollectionResultableApplicatorInterface extends CollectionApplicatorInterface
{
    public function supportsResult(CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams, BuilderRegistryInterface $builderRegistry): bool;

    public function getResult(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams, BuilderRegistryInterface $builderRegistry): iterable;
}
