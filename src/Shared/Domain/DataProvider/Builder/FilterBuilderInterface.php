<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

use Owl\Shared\Domain\DataProvider\Filter\FilterInterface;

interface FilterBuilderInterface extends BuilderInterface
{
    public const NAME = 'filter';

    public function getParamName(): string;

    public function setParamName(string $paramName): self;

    public function getDataFilters(): array;

    public function add(string $name = null, string $filter, string|array $fields = null, array $options = []): self;

    public function get(string $name): FilterInterface;

    public function remove(string $name): self;

    public function has(string $name): bool;

    public function count(): int;

    public function countUnresolved(): int;

    public function all(): array;
}
