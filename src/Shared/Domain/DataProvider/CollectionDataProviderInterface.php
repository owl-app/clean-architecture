<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider;

use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

interface CollectionDataProviderInterface
{
    /**
     * @param class-string $dataClass A persistent object class name.
     */
    public function get(string $dataClass, CollectionTypeInterface $dataProviderType, CollectionRequestParamsInterface $collectionRequestParams): iterable;
}
