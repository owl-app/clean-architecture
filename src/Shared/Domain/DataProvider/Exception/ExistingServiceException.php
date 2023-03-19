<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Exception;

class ExistingServiceException extends \InvalidArgumentException
{
    public function __construct(string $context, string $type)
    {
        parent::__construct(sprintf('%s of type "%s" already exists.', $context, $type));
    }
}
