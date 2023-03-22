<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Mapper;

interface ItemMapperInterface
{
    public function toResponse(object $data): object;
}
