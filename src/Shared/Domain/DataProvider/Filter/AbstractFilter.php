<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Filter;

use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;

abstract class AbstractFilter implements FilterInterface
{
    private string $name;

    private array $fields = [];

    private array $options = [];

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function buildFilter(FilterBuilderInterface $filterBuilder): void
    {
    }
}
