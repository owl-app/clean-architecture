<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm;

use Owl\Shared\Domain\DataProvider\ItemDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Factory\QueryBuilderFactoryInterface;

final class ItemDataProvider implements ItemDataProviderInterface
{
    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuildeFactory,
        private readonly iterable $applicators
    ) {
    }

    public function get(string $dataClass, RequestParamsInterface $collectionRequestParams, ?ItemTypeInterface $itemProviderType = null): ?object
    {
        $queryBuilder = $this->queryBuildeFactory->create($dataClass, $itemProviderType);

        foreach($this->applicators as $applicator) {
            $applicator->applyToItem($queryBuilder, $itemProviderType, $collectionRequestParams, $dataClass);

            // if ($applicator instanceof CollectionResultableApplicatorInterface && $applicator->supportsResult($dataProviderType, $collectionRequestParams)) {
            //     return $applicator->getResult($queryBuilder, $itemProviderType, $collectionRequestParams);
            // }
        }

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }
}