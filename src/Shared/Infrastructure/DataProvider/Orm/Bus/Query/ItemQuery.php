<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query;

use Owl\Shared\Domain\DataProvider\Bus\Query\ItemQueryInterface;
use Owl\Shared\Domain\DataProvider\Mapper\ItemMapperInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;

final class ItemQuery implements ItemQueryInterface
{
    /**
     * @param class-string $model
     */
    public function __construct(
        private string $model,
        private RequestParamsInterface $requestParams,
        private ?ItemTypeInterface $type = null,
        private ?ItemMapperInterface $mapper = null,
    ) {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getType(): ?ItemTypeInterface
    {
        return $this->type;
    }

    public function getRequestParams(): RequestParamsInterface
    {
        return $this->requestParams;
    }

    public function getMapper(): ?ItemMapperInterface
    {
        return $this->mapper;
    }
}
