<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query;

use Owl\Shared\Domain\DataProvider\Bus\Query\CollectionQueryInterface;
use Owl\Shared\Domain\DataProvider\Mapper\CollectionMapperInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

final class CollectionQuery implements CollectionQueryInterface
{
    /**
     * @param class-string $model
     */
    public function __construct(
        private string $model,
        private CollectionTypeInterface $type,
        private CollectionRequestParamsInterface $requestParams,
        private ?CollectionMapperInterface $mapper = null,
    ) {
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getType(): CollectionTypeInterface
    {
        return $this->type;
    }

    public function getRequestParams(): CollectionRequestParamsInterface
    {
        return $this->requestParams;
    }

    public function getMapper(): ?CollectionMapperInterface
    {
        return $this->mapper;
    }
}
