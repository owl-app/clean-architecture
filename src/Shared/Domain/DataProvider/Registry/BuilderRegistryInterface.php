<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Registry;

use Owl\Shared\Domain\DataProvider\Builder\BuilderInterface;

interface BuilderRegistryInterface
{
    public function add(string $identifier, BuilderInterface $builder): void;

    public function get(string $identifier): ?BuilderInterface;

    public function has(string $identifier): bool;
}
