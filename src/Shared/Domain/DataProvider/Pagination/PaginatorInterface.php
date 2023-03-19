<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Pagination;

interface PaginatorInterface extends PartialPaginatorInterface
{
    public function getLastPage(): float;

    public function getTotalItems(): float;
}
