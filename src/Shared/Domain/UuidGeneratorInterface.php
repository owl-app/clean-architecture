<?php

declare(strict_types=1);

namespace Owl\Shared\Domain;

interface UuidGeneratorInterface
{
    public function generate(): string;
}
