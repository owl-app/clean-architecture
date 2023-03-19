<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure;

use Owl\Shared\Domain\UuidGeneratorInterface;
use Ramsey\Uuid\Uuid;

final class RamseyUuidGenerator implements UuidGeneratorInterface
{
    public function generate(): string
    {
        return Uuid::uuid4()->toString();
    }
}
