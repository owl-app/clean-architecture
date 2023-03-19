<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

interface CollectionDataInterface
{
    public function getData(): iterable;
}
