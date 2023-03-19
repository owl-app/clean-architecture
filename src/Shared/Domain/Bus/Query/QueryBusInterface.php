<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\Bus\Query;

interface QueryBusInterface
{
    public function query(QueryInterface $query): mixed;
}
