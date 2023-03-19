<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilder;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistry;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Validation\SortingParametersValidator;
use Owl\Shared\Domain\DataProvider\Validation\SortingParametersValidatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Tests\Fixtures\DummyDataProvider;
use Owl\Tests\Fixtures\DummyEmptyDataProvider;
use Owl\Tests\Fixtures\Entity\Dummy;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class SortApplicatorTest extends TestCase
{
    use ProphecyTrait;


    public function testSetBuilderAddsBuilderToRegistr(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class);
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class);
        $fieldResolverProphecy = $this->prophesize(FieldResolverInterface::class);

        $collectionRequestParamsProphecy->getDefaultSorting()->willReturn([]);
        $collectionRequestParamsProphecy->getQueryParams()->willReturn([]);

        $filterApplicator = new SortApplicator($fieldResolverProphecy->reveal());

        $filterApplicator->setBuilder(
            $builderRegistry,
            $collectionTypeProphecy->reveal(),
            $collectionRequestParamsProphecy->reveal()
        );

        $this->assertEquals(true, $builderRegistry->has(SortBuilderInterface::NAME));
    }

    public function setBuilder(BuilderRegistryInterface $builderRegistry, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $this->builder = new SortBuilder($collectionRequestParams->getDefaultFiltering(), $collectionRequestParams->getQueryParams());
        $builderRegistry->add($this->builder->getName(), $this->builder);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams) : void
    {
        $collectionType->buildSort($this->builder);

        $sorts = $this->builder->getSorting();

        if($sorts) {
            foreach($sorts as $property => $sort) {
                if($this->sortingValidator->validateSortingParameters($this->builder->getAvailable(), $property, $sort)) {
                    $field = $this->fieldResolver->resolveFieldByAddingJoins($queryBuilder, $property);
                    $queryBuilder->addOrderBy($field, $sort);
                }
            }
        }
    }
}
