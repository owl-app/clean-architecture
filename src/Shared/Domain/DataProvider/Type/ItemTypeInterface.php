<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Type;

interface ItemTypeInterface
{
    public function getIdentifiers(): array;
}
