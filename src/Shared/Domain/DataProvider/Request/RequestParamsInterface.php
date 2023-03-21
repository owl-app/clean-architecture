<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Request;

interface RequestParamsInterface
{
    public function getQueryParams(): array;
}
