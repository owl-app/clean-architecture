<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm;

use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;

final class Paginator extends AbstractPaginator implements PaginatorInterface
{
    private ?int $totalItems = null;

    /**
     * @inheritdoc
     */
    public function getLastPage(): float
    {
        if (0 >= $this->maxResults) {
            return 1.;
        }

        return ceil($this->getTotalItems() / $this->maxResults) ?: 1.;
    }

    /**
     * @inheritdoc
     */
    public function getTotalItems(): float
    {
        return (float) ($this->totalItems ?? $this->totalItems = \count($this->paginator));
    }
}
