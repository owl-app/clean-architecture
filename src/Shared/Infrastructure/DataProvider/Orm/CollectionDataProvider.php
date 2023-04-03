<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm;

use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\CollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistry;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Applicator\CollectionResultableApplicatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Factory\QueryBuilderFactoryInterface;

final class CollectionDataProvider implements CollectionDataProviderInterface
{
    public function __construct(
        private readonly QueryBuilderFactoryInterface $queryBuildeFactory,
        private readonly iterable $applicators,
    ) {
    }

    public function get(string $dataClass, CollectionTypeInterface $dataProviderType, CollectionRequestParamsInterface $collectionRequestParams): iterable
    {
        $queryBuilder = $this->queryBuildeFactory->create($dataClass, $dataProviderType);
        $builderRegistry = new BuilderRegistry();

        foreach ($this->applicators as $applicator) {
            if ($applicator instanceof BuilderAwareInterface) {
                $applicator->setBuilder($builderRegistry, $dataProviderType, $collectionRequestParams);
            }

            $applicator->applyToCollection($queryBuilder, $dataProviderType, $collectionRequestParams);

            if ($applicator instanceof CollectionResultableApplicatorInterface && $applicator->supportsResult($dataProviderType, $collectionRequestParams, $builderRegistry)) {
                return $applicator->getResult($queryBuilder, $dataProviderType, $collectionRequestParams, $builderRegistry);
            }
        }

        return $queryBuilder->getQuery()->getResult();
    }
}
