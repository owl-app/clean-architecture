<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;
use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;
use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;
use Owl\Shared\Domain\DataProvider\Pagination\PartialPaginatorInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistry;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Tests\Fixtures\DummyDataProvider;
use Owl\Tests\Fixtures\Entity\Dummy;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class PaginationApplicatorTest extends TestCase
{
    use ProphecyTrait;

    public function testSetBuilderAddsBuilderToRegistr(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class);
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParamsProphecy->getDefaultPagination()->willReturn([]);
        $collectionRequestParamsProphecy->getQueryParams()->willReturn([]);

        $filterApplicator = new PaginationApplicator();

        $filterApplicator->setBuilder(
            $builderRegistry,
            $collectionTypeProphecy->reveal(),
            $collectionRequestParamsProphecy->reveal(),
        );

        $this->assertEquals(true, $builderRegistry->has(PaginationBuilderInterface::NAME));
    }

    /**
     * @dataProvider getConfigPaginationWithQueryParams
     */
    public function testApplyToCollectionWithDefaultConfig(array $paginationConfig, array $queryParams, int $firstResult, int $maxResults): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult($firstResult)->willReturn($queryBuilderProphecy)->shouldBeCalled();
        $queryBuilderProphecy->setMaxResults($maxResults)->shouldBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultPagination()->willReturn($paginationConfig)->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn($queryParams)->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }

    public function getConfigPaginationWithQueryParams(): array
    {
        return [
            'without config and query params' => [
                [],
                [],
                0,
                10,
            ],
            'with item per page 0' => [
                [
                    'allowed_per_page' => [0],
                ],
                [
                    'per-page' => 0,
                ],
                0,
                0,
            ],
            'without query params' => [
                [
                    'fetch_join_collection' => true,
                    'use_output_walkers' => false,
                    'param_per_page_name' => 'per-page',
                    'param_page_name' => 'page',
                    'default_per_page' => 20,
                    'allowed_per_page' => [10, 20, 50, 100],
                ],
                [],
                0,
                20,
            ],
            'per page query params' => [
                [
                    'fetch_join_collection' => true,
                    'use_output_walkers' => false,
                    'param_per_page_name' => 'per-page',
                    'param_page_name' => 'page',
                    'default_per_page' => 20,
                    'allowed_per_page' => [10, 20, 50, 100],
                ],
                [
                    'per-page' => 10,
                ],
                0,
                10,
            ],
            'page query params' => [
                [
                    'fetch_join_collection' => true,
                    'use_output_walkers' => false,
                    'param_per_page_name' => 'per-page',
                    'param_page_name' => 'page',
                    'default_per_page' => 20,
                    'allowed_per_page' => [10, 20, 50, 100],
                ],
                [
                    'page' => 2,
                ],
                20,
                20,
            ],
            'per page and page query params' => [
                [
                    'fetch_join_collection' => true,
                    'use_output_walkers' => false,
                    'param_per_page_name' => 'per-page',
                    'param_page_name' => 'page',
                    'default_per_page' => 20,
                    'allowed_per_page' => [10, 20, 50, 100],
                ],
                [
                    'per-page' => 20,
                    'page' => 3,
                ],
                40,
                20,
            ],
        ];
    }

    public function testApplyToCollectionWithItemPerPageZeroAndPage2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Page should not be greater than 1 if limit is equal to 0');

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult(0)->willReturn($queryBuilderProphecy)->shouldNotBeCalled();
        $queryBuilderProphecy->setMaxResults(0)->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['per-page' => 0, 'page' => 2])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }

    public function testApplyToCollectionWithItemPerPageLessThan0(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Limit should not be less than 0');

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult(0)->willReturn($queryBuilderProphecy)->shouldNotBeCalled();
        $queryBuilderProphecy->setMaxResults(0)->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['per-page' => -10, 'page' => 2])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }

    public function testApplyToCollectionWithInvalidPerPage(): void
    {
        $allowedPerPage = [10, 20, 50, 100];
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf('Not allowed per page, available: %s', implode(',', $allowedPerPage)));

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult(0)->willReturn($queryBuilderProphecy)->shouldNotBeCalled();
        $queryBuilderProphecy->setMaxResults(0)->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);

        $collectionRequestParams->getDefaultPagination()->willReturn(['allowed_per_page' => $allowedPerPage])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn(['per-page' => 1000])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
    }

    public function testPaginationDisabled(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider(['pagination' => ['hasPagination' => false]]);

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult(Argument::any())->shouldNotBeCalled();
        $queryBuilderProphecy->setMaxResults(Argument::any())->shouldNotBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn([])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
        $this->assertFalse($applicator->supportsResult($collectionType, $collectionRequestParams->reveal(), $builderRegistry));
    }

    public function testSupportsResult(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->setFirstResult(0)->willReturn($queryBuilderProphecy)->shouldBeCalled();
        $queryBuilderProphecy->setMaxResults(10)->shouldBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn([])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());
        $applicator->applyToCollection($queryBuilder, $collectionType, $collectionRequestParams->reveal());
        $this->assertTrue($applicator->supportsResult($collectionType, $collectionRequestParams->reveal(), $builderRegistry));
    }

    public function testGetResult(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $dummyMetadata = new ClassMetadata(Dummy::class);

        $entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);
        $entityManagerProphecy->getConfiguration()->willReturn(new Configuration());
        $entityManagerProphecy->getClassMetadata(Dummy::class)->willReturn($dummyMetadata);

        $queryBuilder = new QueryBuilder($entityManagerProphecy->reveal());
        $queryBuilder->select('o');
        $queryBuilder->from(Dummy::class, 'o');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults(42);

        $query = new Query($entityManagerProphecy->reveal());
        $entityManagerProphecy->createQuery($queryBuilder->getDQL())->willReturn($query);

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn([])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());

        $result = $applicator->getResult($queryBuilder, $collectionType, $collectionRequestParams->reveal(), $builderRegistry);

        $this->assertInstanceOf(PartialPaginatorInterface::class, $result);
        $this->assertInstanceOf(PaginatorInterface::class, $result);
    }

    public function testGetResultWithoutDistinct(): void
    {
        $builderRegistry = new BuilderRegistry();
        $collectionType = new DummyDataProvider();
        $dummyMetadata = new ClassMetadata(Dummy::class);

        $entityManagerProphecy = $this->prophesize(EntityManagerInterface::class);
        $entityManagerProphecy->getConfiguration()->willReturn(new Configuration());
        $entityManagerProphecy->getClassMetadata(Dummy::class)->willReturn($dummyMetadata);

        $queryBuilder = new QueryBuilder($entityManagerProphecy->reveal());
        $queryBuilder->select('o');
        $queryBuilder->from(Dummy::class, 'o');
        $queryBuilder->setFirstResult(0);
        $queryBuilder->setMaxResults(42);

        $query = new Query($entityManagerProphecy->reveal());
        $entityManagerProphecy->createQuery($queryBuilder->getDQL())->willReturn($query);

        $collectionRequestParams = $this->prophesize(CollectionRequestParamsInterface::class);
        $collectionRequestParams->getDefaultPagination()->willReturn([])->shouldBeCalled();
        $collectionRequestParams->getQueryParams()->willReturn([])->shouldBeCalled();

        $applicator = new PaginationApplicator();
        $applicator->setBuilder($builderRegistry, $collectionType, $collectionRequestParams->reveal());

        $result = $applicator->getResult($queryBuilder, $collectionType, $collectionRequestParams->reveal(), $builderRegistry);

        $this->assertInstanceOf(PartialPaginatorInterface::class, $result);
        $this->assertInstanceOf(PaginatorInterface::class, $result);

        $this->assertFalse($query->getHint(CountWalker::HINT_DISTINCT));
    }
}
