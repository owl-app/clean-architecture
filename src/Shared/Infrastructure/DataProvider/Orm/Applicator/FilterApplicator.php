<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilder;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Registry\FilterRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Util\QueryNameGenerator;

class FilterApplicator implements CollectionApplicatorInterface, BuilderAwareInterface
{
    private FilterBuilderInterface $builder;

    public function __construct(private readonly FieldResolverInterface $fieldResolver, private readonly FilterRegistryInterface $registry)
    {
    }

    public function setBuilder(BuilderRegistryInterface $builderRegistry, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $this->builder = new FilterBuilder($this->registry, $collectionRequestParams->getDefaultFiltering(), $collectionRequestParams->getQueryParams());
        $builderRegistry->add($this->builder->getName(), $this->builder);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $queryNameGenerator = new QueryNameGenerator();
        $dataFilters = $this->builder->getDataFilters();
        $collectionType->buildFilters($this->builder);

        if ($dataFilters) {
            foreach ($this->builder->all() as $name => $filter) {
                $resolvedFields = [];

                foreach ($filter->getFields() as $field) {
                    $resolvedFields[$field] = $this->fieldResolver->resolveFieldByAddingJoins($queryBuilder, $field);
                }

                $filter->buildQuery($queryBuilder, $queryNameGenerator, $dataFilters[$name] ?? null, $resolvedFields, $filter->getOptions());
            }
        }
    }
}
