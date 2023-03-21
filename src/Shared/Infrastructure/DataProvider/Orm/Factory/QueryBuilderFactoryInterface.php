<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Factory;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

interface QueryBuilderFactoryInterface
{
    /**
     * @param class-string $dataClass A persistent object class name.
     */
    public function create(string $dataClass, BuildableQueryBuilderInterface|CollectionTypeInterface|ItemTypeInterface $collectionType = null): QueryBuilder;
}
