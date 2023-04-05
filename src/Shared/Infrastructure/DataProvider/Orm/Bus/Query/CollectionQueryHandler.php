<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query;

use IteratorAggregate;
use Owl\Shared\Domain\DataProvider\Bus\Query\CollectionQueryHandlerInterface;
use Owl\Shared\Domain\DataProvider\Bus\Query\CollectionQueryInterface;
use Owl\Shared\Domain\DataProvider\CollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Data\CollectionData;
use Owl\Shared\Domain\DataProvider\Data\CollectionDataInterface;
use Owl\Shared\Domain\DataProvider\Data\CollectionPaginatedData;
use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;
use Traversable;

class CollectionQueryHandler implements CollectionQueryHandlerInterface
{
    public function __construct(private readonly CollectionDataProviderInterface $collectionDataProvider)
    {
    }

    public function __invoke(CollectionQueryInterface $collectionQuery): CollectionDataInterface
    {
        /** @var PaginatorInterface&IteratorAggregate&iterable $data */
        $data = $this->collectionDataProvider->get(
            $collectionQuery->getModel(),
            $collectionQuery->getType(),
            $collectionQuery->getRequestParams(),
        );

        /** @var Traversable|null $mappedData */
        $mappedData = $collectionQuery->getMapper()?->toResponse($data);

        if ($data instanceof PaginatorInterface) {
            return new CollectionPaginatedData($data, $mappedData);
        }

        return new CollectionData($mappedData ?? $data);
    }
}
