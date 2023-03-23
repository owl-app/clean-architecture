<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\Persistence;

abstract class AbstractBaseEntity implements BaseEntityInterface
{
    protected string $id;

    public function getId(): string
    {
        return $this->id;
    }
}
