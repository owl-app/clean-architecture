<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider;

use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

interface ItemDataProviderInterface
{
    /**
     * @param class-string $dataClass A persistent object class name.
     */
    public function get(string $dataClass, CollectionRequestParamsInterface $collectionRequestParams, ?CollectionTypeInterface $dataProviderType = null): iterable;
}
