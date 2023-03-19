<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

interface CollectionMetadataInterface
{
    public function getCurrentPage(): float;

    public function getPerPage(): float;

    public function getLastPage(): float;

    public function getTotalCount(): float;
}
