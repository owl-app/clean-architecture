<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Factory;

use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;

interface RequestParamsFactoryInterface
{
    public function create(string $class, array $parameters, array $query): RequestParamsInterface;
}
