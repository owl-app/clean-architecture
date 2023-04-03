<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Bus\Query;

use Owl\Shared\Domain\Bus\Query\QueryInterface;
use Owl\Shared\Domain\DataProvider\Mapper\ItemMapperInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;

interface ItemQueryInterface extends QueryInterface
{
    /**
     * @return class-string
     */
    public function getModel(): string;

    public function getType(): ?ItemTypeInterface;

    public function getRequestParams(): RequestParamsInterface;

    public function getMapper(): ?ItemMapperInterface;
}
