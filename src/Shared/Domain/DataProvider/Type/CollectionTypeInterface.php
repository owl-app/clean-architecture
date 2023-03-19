<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Type;

use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;

interface CollectionTypeInterface
{
    public function buildFilters(FilterBuilderInterface $filterBuilder): void;

    public function buildSort(SortBuilderInterface $sortBuilder): void;

    public function buildPagination(PaginationBuilderInterface $paginationBuilder): void;
}
