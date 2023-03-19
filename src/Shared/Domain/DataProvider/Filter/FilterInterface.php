<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Filter;

use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;

interface FilterInterface
{
    public function getName(): string;

    public function setName(string $name): void;

    public function getFields(): array;

    public function setFields(array $fields): void;

    public function getOptions(): array;

    public function setOptions(array $options): void;

    public function buildFilter(FilterBuilderInterface $filterBuilder): void;
}
