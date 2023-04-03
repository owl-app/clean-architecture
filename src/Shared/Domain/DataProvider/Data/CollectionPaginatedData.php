<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

use IteratorAggregate;
use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;

class CollectionPaginatedData extends CollectionData implements PaginatedDataInterface
{
    public CollectionMetadataInterface $metadata;

    public function __construct(PaginatorInterface&IteratorAggregate $paginator, ?iterable $mappedData = null)
    {
        parent::__construct($mappedData ?? $paginator->getIterator());
        $this->metadata = new CollectionMetadata($paginator);
    }

    public function getData(): iterable
    {
        return $this->data;
    }

    public function getMetadata(): CollectionMetadataInterface
    {
        return $this->metadata;
    }
}
