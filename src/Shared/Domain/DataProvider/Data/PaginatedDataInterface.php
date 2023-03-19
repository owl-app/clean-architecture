<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

interface PaginatedDataInterface
{
    public function getMetadata(): CollectionMetadataInterface;
}
