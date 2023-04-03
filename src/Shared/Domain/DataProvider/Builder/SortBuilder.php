<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

class SortBuilder implements SortBuilderInterface
{
    private string $paramName;

    private array $available;

    public function __construct(private readonly array $defaultParameters, private readonly array $queryParams)
    {
        $this->paramName = $defaultParameters['param_name'] ?? 'sort';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getParamName(): string
    {
        return $this->paramName;
    }

    public function setParamName(string $paramName): self
    {
        $this->paramName = $paramName;

        return $this;
    }

    public function getAvailable(): array
    {
        return $this->available;
    }

    public function setAvailable(array $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getSorting(): array
    {
        if (isset($this->queryParams[$this->paramName]) && is_array($this->queryParams[$this->paramName])) {
            return $this->queryParams[$this->paramName];
        }

        return [];
    }
}
