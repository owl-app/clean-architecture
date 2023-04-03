<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;

final class CollectionMetadata implements CollectionMetadataInterface
{
    public function __construct(
        private readonly PaginatorInterface $paginator,
    ) {
    }

    public function getCurrentPage(): float
    {
        return $this->paginator->getCurrentPage();
    }

    public function getPerPage(): float
    {
        return $this->paginator->getItemsPerPage();
    }

    public function getLastPage(): float
    {
        return $this->paginator->getLastPage();
    }

    public function getTotalCount(): float
    {
        return $this->paginator->getTotalItems();
    }
}
