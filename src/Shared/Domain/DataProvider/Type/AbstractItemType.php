<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Type;

abstract class AbstractItemType implements ItemTypeInterface
{
    public function getIdentifiers(): array
    {
        return [];
    }
}
