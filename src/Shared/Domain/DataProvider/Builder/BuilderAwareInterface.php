<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

interface BuilderAwareInterface
{
    public function setBuilder(BuilderRegistryInterface $builderRegistry, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void;
}
