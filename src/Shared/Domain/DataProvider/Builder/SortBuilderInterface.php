<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

interface SortBuilderInterface extends BuilderInterface
{
    public const NAME = 'sort';

    public function getParamName(): string;

    public function setParamName(string $paramName): self;

    public function getAvailable(): array;

    public function setAvailable(array $available): self;

    public function getSorting(): array;
}
