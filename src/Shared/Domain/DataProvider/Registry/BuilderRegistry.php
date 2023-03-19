<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Registry;

use Owl\Shared\Domain\DataProvider\Builder\BuilderInterface;

final class BuilderRegistry implements BuilderRegistryInterface
{
    private array $builders = [];

    public function add(string $identifier, BuilderInterface $builder): void
    {
        $this->builders[$identifier] = $builder;
    }

    public function get(string $identifier): ?BuilderInterface
    {
        if (!$this->has($identifier)) {
            return null;
        }

        return $this->builders[$identifier];
    }

    public function has(string $identifier): bool
    {
        return isset($this->builders[$identifier]);
    }
}
