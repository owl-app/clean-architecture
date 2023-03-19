<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Validation;

interface SortingParametersValidatorInterface
{
    public function validateSortingParameters(array $available, string $field, string $typeSorting): bool;
}
