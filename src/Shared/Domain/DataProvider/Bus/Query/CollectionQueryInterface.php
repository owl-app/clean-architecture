<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Bus\Query;

use Owl\Shared\Domain\Bus\Query\QueryInterface;
use Owl\Shared\Domain\DataProvider\Mapper\CollectionMapperInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;

interface CollectionQueryInterface extends QueryInterface
{
    /**
     * @return class-string
     */
    public function getModel(): string;

    public function getType(): ?CollectionTypeInterface;

    public function getRequestParams(): CollectionRequestParamsInterface;

    public function getMapper(): ?CollectionMapperInterface;
}
