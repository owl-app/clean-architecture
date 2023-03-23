<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider;

use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;

interface ItemDataProviderInterface
{
    /**
     * @param class-string $dataClass A persistent object class name.
     */
    public function get(string $dataClass, RequestParamsInterface $itemRequestParams, ?ItemTypeInterface $itemProviderType = null): ?object;
}
