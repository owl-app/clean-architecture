<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Validation;

use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;

final class PaginationParametersValidator implements PaginationParametersValidatorInterface
{
    public function validatePaginationParameters(int $offset, int $limit, int $page, array $allowedPerPage): void
    {
        if (0 === $limit && 1 < $page) {
            throw new InvalidArgumentException('Page should not be greater than 1 if limit is equal to 0');
        }

        if (0 > $limit) {
            throw new InvalidArgumentException('Limit should not be less than 0');
        }

        if (!in_array($limit, $allowedPerPage)) {
            throw new InvalidArgumentException(\sprintf('Not allowed per page, available: %s', implode(',', $allowedPerPage)));
        }
    }
}
