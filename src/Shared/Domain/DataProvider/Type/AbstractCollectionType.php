<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Type;

use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;

abstract class AbstractCollectionType implements CollectionTypeInterface
{
    public function buildPagination(PaginationBuilderInterface $paginationBuilder): void
    {
    }
}
