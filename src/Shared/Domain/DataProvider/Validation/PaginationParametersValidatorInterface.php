<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Validation;

interface PaginationParametersValidatorInterface
{
    public function validatePaginationParameters(int $offset, int $limit, int $page, array $allowedPerPage): void;
}
