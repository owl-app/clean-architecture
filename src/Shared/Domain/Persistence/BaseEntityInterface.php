<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\Persistence;

interface BaseEntityInterface
{
    public function getId(): string;
}
