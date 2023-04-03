<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Exception;

class NonExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type, array $existingServices)
    {
        parent::__construct(sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst($context),
            $type,
            $context,
            implode('", "', $existingServices),
        ));
    }
}
