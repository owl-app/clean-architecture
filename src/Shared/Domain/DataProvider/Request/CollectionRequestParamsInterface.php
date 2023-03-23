<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Request;

interface CollectionRequestParamsInterface extends RequestParamsInterface
{
    public function getDefaultFiltering(): array;

    public function getDefaultSorting(): array;

    public function getDefaultPagination(): array;
}
