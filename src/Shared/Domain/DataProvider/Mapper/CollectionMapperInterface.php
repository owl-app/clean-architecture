<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Mapper;

use Traversable;

interface CollectionMapperInterface
{
    public function toResponse(Traversable|array $data): iterable;
}
