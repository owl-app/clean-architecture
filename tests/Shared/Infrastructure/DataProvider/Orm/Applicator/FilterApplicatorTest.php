<?php

declare(strict_types=1);

namespace Owl\Tests\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\FilterBuilderInterface;
use Owl\Shared\Domain\DataProvider\Exception\NonExistingServiceException;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistry;
use Owl\Shared\Domain\DataProvider\Registry\FilterRegistry;
use Owl\Shared\Domain\DataProvider\Registry\FilterRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Applicator\FilterApplicator;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;
use Owl\Tests\Fixtures\DummyDataProvider;
use Owl\Tests\Fixtures\DummyFilter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class FilterApplicatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSetBuilderAddsBuilderToRegistr(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class);
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class);
        $fieldResolverProphecy = $this->prophesize(FieldResolverInterface::class);
        $filterRegistryProphecy = $this->prophesize(FilterRegistryInterface::class);

        $collectionRequestParamsProphecy->getDefaultFiltering()->willReturn([]);
        $collectionRequestParamsProphecy->getQueryParams()->willReturn([]);

        $filterApplicator = new FilterApplicator(
            $fieldResolverProphecy->reveal(),
            $filterRegistryProphecy->reveal(),
        );

        $filterApplicator->setBuilder(
            $builderRegistry,
            $collectionTypeProphecy->reveal(),
            $collectionRequestParamsProphecy->reveal(),
        );

        $this->assertEquals(true, $builderRegistry->has(FilterBuilderInterface::NAME));
    }

    public function testApplyToCollectionWithValidFilter()
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->addSelect('alias1.field1')->shouldBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $filterProphecy = new DummyFilter();

        $filterRegistry = new FilterRegistry('Owl\Shared\Domain\DataProvider\Filter\FilterInterface');
        $filterRegistry->register(DummyFilter::class, $filterProphecy);
        $fieldResolver = $this->prophesize(FieldResolverInterface::class);
        $collectionType = new DummyDataProvider(['filters' => [
            ['name' => 'filterName', 'filter' => DummyFilter::class, 'fields' => ['field1']]],
        ]);
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultFiltering()->willReturn(['param_filter_name' => 'filters'])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['filters' => ['filterName' => ['field1' => 'test']]])->shouldBeCalled();

        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, 'field1')->willReturn('alias1.field1')->shouldBeCalled();

        $applicator = new FilterApplicator($fieldResolver->reveal(), $filterRegistry);
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $filterBuilder = $builderRegistry->get(FilterBuilderInterface::NAME);
        $filterAfterBuild = $filterBuilder->get('filterName');

        $this->assertEquals(['filterName' => ['field1' => 'test']], $filterBuilder->getDataFilters());
        $this->assertEquals(true, $filterBuilder->has('filterName'));
        $this->assertEquals('filterName', $filterAfterBuild->getName());
        $this->assertEquals(['field1'], $filterAfterBuild->getFields());
        $this->assertEquals([], $filterAfterBuild->getOptions());
    }

    public function testApplyToCollectionWithNonExistFilter(): void
    {
        $this->expectException(NonExistingServiceException::class);
        $this->expectExceptionMessage(\sprintf(
            '%s service "%s" does not exist, available %s services: "%s"',
            ucfirst('filter'),
            DummyFilter::class,
            'filter',
            '',
        ));

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();

        $filterRegistry = new FilterRegistry('Owl\Shared\Domain\DataProvider\Filter\FilterInterface');
        $fieldResolver = $this->prophesize(FieldResolverInterface::class);
        $collectionType = new DummyDataProvider(['filters' => [
            ['name' => 'filterName', 'filter' => DummyFilter::class, 'fields' => ['field1']]],
        ]);
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultFiltering()->willReturn(['param_filter_name' => 'filters'])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['filters' => ['filterName' => ['field1' => 'test']]])->shouldBeCalled();

        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, 'field1')->willReturn('alias1.field1')->shouldNotBeCalled();

        $applicator = new FilterApplicator($fieldResolver->reveal(), $filterRegistry);
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }

    public function testApplyToCollectionWithoutFilters(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();

        $filterRegistry = new FilterRegistry('Owl\Shared\Domain\DataProvider\Filter\FilterInterface');
        $fieldResolver = $this->prophesize(FieldResolverInterface::class);
        $collectionType = new DummyDataProvider();
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultFiltering()->willReturn(['param_filter_name' => 'filters'])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['filters' => ['filterName' => ['field1' => 'test']]])->shouldBeCalled();

        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, 'field1')->willReturn('alias1.field1')->shouldNotBeCalled();

        $applicator = new FilterApplicator($fieldResolver->reveal(), $filterRegistry);
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $filterBuilder = $builderRegistry->get(FilterBuilderInterface::NAME);

        $this->assertEquals(0, $filterBuilder->count());
        $this->assertEquals(false, $filterRegistry->has(FilterBuilderInterface::NAME));
    }

    public function testApplyToCollectionWithoutFilteringQueryData(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->addSelect('alias1.field1')->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $filterProphecy = new DummyFilter();

        $filterRegistry = new FilterRegistry('Owl\Shared\Domain\DataProvider\Filter\FilterInterface');
        $filterRegistry->register(DummyFilter::class, $filterProphecy);
        $fieldResolver = $this->prophesize(FieldResolverInterface::class);
        $collectionType = new DummyDataProvider(['filters' => [
            ['name' => 'filterName', 'filter' => DummyFilter::class, 'fields' => ['field1']]],
        ]);
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultFiltering()->willReturn(['param_filter_name' => 'filters'])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn([])->shouldBeCalled();

        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, '')->shouldNotBeCalled();

        $applicator = new FilterApplicator($fieldResolver->reveal(), $filterRegistry);
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $filterBuilder = $builderRegistry->get(FilterBuilderInterface::NAME);
        $filterAfterBuild = $filterBuilder->get('filterName');

        $this->assertEquals([], $filterBuilder->getDataFilters());
        $this->assertEquals(true, $filterBuilder->has('filterName'));
        $this->assertEquals('filterName', $filterAfterBuild->getName());
        $this->assertEquals(['field1'], $filterAfterBuild->getFields());
        $this->assertEquals([], $filterAfterBuild->getOptions());
    }
}
