<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm;

use Owl\Shared\Domain\DataProvider\ItemDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Applicator\ItemResultableApplicatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Factory\QueryBuilderFactoryInterface;

final class ItemDataProvider implements ItemDataProviderInterface
{
    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuildeFactory,
        private readonly iterable $applicators,
    ) {
    }

    public function get(string $dataClass, RequestParamsInterface $itemRequestParams, ?ItemTypeInterface $itemProviderType = null): ?object
    {
        $queryBuilder = $this->queryBuildeFactory->create($dataClass, $itemProviderType);

        foreach ($this->applicators as $applicator) {
            $applicator->applyToItem($queryBuilder, $itemProviderType, $itemRequestParams, $dataClass);

            if ($applicator instanceof ItemResultableApplicatorInterface && $applicator->supportsResult($itemProviderType, $itemRequestParams)) {
                return $applicator->getResult($queryBuilder, $itemProviderType, $itemRequestParams);
            }
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}
