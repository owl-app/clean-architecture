<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query;

use Owl\Shared\Domain\DataProvider\Bus\Query\ItemQueryHandlerInterface;
use Owl\Shared\Domain\DataProvider\Bus\Query\ItemQueryInterface;
use Owl\Shared\Domain\DataProvider\ItemDataProviderInterface;

class ItemQueryHandler implements ItemQueryHandlerInterface
{
    public function __construct(private readonly ItemDataProviderInterface $itemDataProvider)
    {
    }

    public function __invoke(ItemQueryInterface $itemQuery):? object
    {
        /** @var object $data */
        $data = $this->itemDataProvider->get(
            $itemQuery->getModel(),
            $itemQuery->getRequestParams(),
            $itemQuery->getType()
        );
        $mapper = $itemQuery->getMapper();
        /** @var object|null $mappedData */
        $mappedData = null;

        if(!is_null($mapper)) {
            $mappedData = $mapper->toResponse($data);
        }

        return $mappedData ?? $data;
    }
}
