<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Request;

interface CollectionRequestParamsInterface
{
    public function getQueryParams(): array;

    public function getDefaultFiltering(): array;

    public function getDefaultSorting(): array;

    public function getDefaultPagination(): array;
}
