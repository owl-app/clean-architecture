<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\From as ExprFrom;
use Doctrine\ORM\Query\Expr\Join as ExprJoin;
use Doctrine\ORM\Query\Expr\OrderBy as ExprOrderBy;
use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\SortBuilderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistry;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Domain\DataProvider\Validation\SortingParametersValidatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolver;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;
use Owl\Tests\Fixtures\DummyDataProvider;
use PHPUnit\Framework\TestCase;
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
            $collectionRequestParamsProphecy->reveal(),
        );

        $this->assertEquals(true, $builderRegistry->has(SortBuilderInterface::NAME));
    }

    /**
     * @dataProvider getValidConfig
     */
    public function testApplyToCollectionWithValidOrderByConfig(string $field, string $sort, array $confgiSorting, array $queryParams): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->getDQLPart('orderBy')->shouldBeCalled()->willReturn([]);
        $queryBuilderProphecy->addOrderBy('o.' . $field, $sort)->shouldBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider(['sort' => ['available' => [$field]]]);

        $fieldResolver = $this->prophesize(FieldResolverInterface::class);
        $sortingParametersValidatorProphecy = $this->prophesize(SortingParametersValidatorInterface::class);
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultSorting()->willReturn($confgiSorting)->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn($queryParams)->shouldBeCalled();

        $sortingParametersValidatorProphecy->validateSortingParameters([$field], $field, $sort)->willReturn(true)->shouldBeCalled();
        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, $field)->willReturn('o.' . $field)->shouldBeCalled();

        $applicator = new SortApplicator($fieldResolver->reveal(), $sortingParametersValidatorProphecy->reveal());
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $sortBuilder = $builderRegistry->get(SortBuilderInterface::NAME);

        $this->assertEquals($confgiSorting['param_name'] ?? 'sort', $sortBuilder->getParamName());
        $this->assertEquals([$field], $sortBuilder->getAvailable());
        $this->assertEquals([$field => $sort], $sortBuilder->getSorting());
    }

    public function getValidConfig(): array
    {
        return [
            'with default config' => [
                'field',
                'asc',
                [],
                ['sort' => ['field' => 'asc']],
            ],
            'with config' => [
                'field',
                'asc',
                ['param_name' => 'sort_test'],
                ['sort_test' => ['field' => 'asc']],
            ],
            'with config and directoion desc' => [
                'field',
                'desc',
                ['param_name' => 'sort_test'],
                ['sort_test' => ['field' => 'desc']],
            ],
        ];
    }

    /**
     * @dataProvider getInvalidConfig
     */
    public function testApplyToCollectionWithInvalidOrderByConfig(string $field, string $sort, array $confgiSorting, array $queryParams): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->getDQLPart('orderBy')->shouldBeCalled()->willReturn([]);
        $queryBuilderProphecy->addOrderBy('o.' . $field, $sort)->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider(['sort' => ['available' => [$field]]]);

        $fieldResolver = $this->prophesize(FieldResolverInterface::class);

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultSorting()->willReturn($confgiSorting)->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn($queryParams)->shouldBeCalled();

        $fieldResolver->resolveFieldByAddingJoins($queryBuilder, $field)->willReturn('o.' . $field)->shouldNotBeCalled();

        $applicator = new SortApplicator($fieldResolver->reveal());
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $sortBuilder = $builderRegistry->get(SortBuilderInterface::NAME);

        $this->assertEquals($confgiSorting['param_name'] ?? 'sort', $sortBuilder->getParamName());
        $this->assertEquals([$field], $sortBuilder->getAvailable());
    }

    public function getInvalidConfig(): array
    {
        return [
            'with invalid sort param' => [
                'field',
                'asc',
                ['param_name' => 'sort_test'],
                ['sort' => ['field' => 'asc']],
            ],
            'with invalid direction' => [
                'field',
                'desc',
                ['param_name' => 'sort'],
                ['sort' => ['field' => 'desc_invalid']],
            ],
            'with invalid no direction' => [
                'field',
                '',
                ['param_name' => 'sort'],
                ['sort' => ['field' => '']],
            ],
            'with not available field' => [
                'field',
                'desc',
                ['param_name' => 'sort'],
                ['sort' => ['field_test' => 'desc']],
            ],
        ];
    }

    /**
     * @dataProvider provideExistingJoinCases
     */
    public function testApplyToCollectionWithOrderWithAssociation(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);

        $queryBuilderProphecy->getDQLPart('orderBy')->shouldBeCalled()->willReturn([]);
        $queryBuilderProphecy->getRootAliases()->shouldBeCalled()->willReturn(['o']);
        $queryBuilderProphecy->getAllAliases()->willReturn(['o', 'test_a1'])->shouldBeCalled();
        $queryBuilderProphecy->getDQLPart('join')->willReturn([
            'o' => [
                new ExprJoin(ExprJoin::LEFT_JOIN, 'o.test', 'test_a1'),
            ],
        ])->shouldBeCalled();
        $queryBuilderProphecy->getDQLPart('from')->willReturn([
            new ExprFrom('test_from', 'o'),
        ])->shouldBeCalled();

        $classMetadataProphecy = $this->prophesize(ClassMetadata::class);
        $classMetadataProphecy->getAssociationMapping('test')->willReturn([
            'targetEntity' => 'targetEntity',
        ]);

        $emProphecy = $this->prophesize(EntityManager::class);
        $emProphecy->getClassMetadata('test_from')->shouldBeCalled()->willReturn($classMetadataProphecy->reveal());
        $emProphecy->getClassMetadata('targetEntity')->shouldBeCalled()->willReturn($this->prophesize(ClassMetadata::class)->reveal());

        $queryBuilderProphecy->addOrderBy('test_a1.description', 'asc')->shouldBeCalled();

        $queryBuilderProphecy->getEntityManager()->shouldBeCalled()->willReturn($emProphecy->reveal());
        $queryBuilderProphecy->getRootAliases()->shouldBeCalled()->willReturn(['o']);

        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $fieldResolver = new FieldResolver();
        $collectionType = new DummyDataProvider(['sort' => ['available' => ['test_a1.description']]]);

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultSorting()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['sort' => ['test_a1.description' => 'asc']])->shouldBeCalled();

        $applicator = new SortApplicator($fieldResolver);
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());

        $sortBuilder = $builderRegistry->get(SortBuilderInterface::NAME);

        $this->assertEquals('sort', $sortBuilder->getParamName());
        $this->assertEquals(['test_a1.description'], $sortBuilder->getAvailable());
    }

    public function provideExistingJoinCases(): iterable
    {
        yield [ExprJoin::LEFT_JOIN];
        yield [ExprJoin::INNER_JOIN];
    }

    public function testApplyToCollectionWithExistingOrderByDql(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->getDQLPart('orderBy')->shouldBeCalled()->willReturn([new ExprOrderBy('o.title')]);
        $queryBuilderProphecy->addOrderBy('o.description', 'asc')->shouldNotBeCalled();

        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $fieldResolver = new FieldResolver();
        $collectionType = new DummyDataProvider(['sort' => ['available' => ['description']]]);

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $sortingParametersValidatorProphecy = $this->prophesize(SortingParametersValidatorInterface::class);

        $sortingParametersValidatorProphecy->validateSortingParameters(['description'], 'description', 'asc')->shouldNotBeCalled();
        $collectionRequestParams->getDefaultSorting()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['sort' => ['description' => 'asc']])->shouldBeCalled();

        $applicator = new SortApplicator($fieldResolver, $sortingParametersValidatorProphecy->reveal());
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }
}
