<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilder;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Domain\DataProvider\Validation\SortingParametersValidator;
use Owl\Shared\Domain\DataProvider\Validation\SortingParametersValidatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;

class SortApplicator implements CollectionApplicatorInterface, BuilderAwareInterface
{
    private SortBuilderInterface $builder;

    private SortingParametersValidatorInterface $sortingValidator;

    public function __construct(private readonly FieldResolverInterface $fieldResolver, ?SortingParametersValidatorInterface $sortingValidator = null)
    {
        $this->sortingValidator = $sortingValidator ?? new SortingParametersValidator();
    }

    public function setBuilder(BuilderRegistryInterface $builderRegistry, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $this->builder = new SortBuilder($collectionRequestParams->getDefaultSorting(), $collectionRequestParams->getQueryParams());
        $builderRegistry->add($this->builder->getName(), $this->builder);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $orderByDqlPart = $queryBuilder->getDQLPart('orderBy');
        if (\is_array($orderByDqlPart) && \count($orderByDqlPart) > 0) {
            return;
        }

        $collectionType->buildSort($this->builder);

        $sorts = $this->builder->getSorting();

        if ($sorts) {
            foreach ($sorts as $property => $sort) {
                if ($this->sortingValidator->validateSortingParameters($this->builder->getAvailable(), $property, $sort)) {
                    $field = $this->fieldResolver->resolveFieldByAddingJoins($queryBuilder, $property);
                    $queryBuilder->addOrderBy($field, $sort);
                }
            }
        }
    }
}
