<?php

declare(strict_types=1);

namespace Owl\Tests\Shared\Infrastructure\DataProvider\Orm;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Applicator\CollectionApplicatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Applicator\CollectionResultableApplicatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\CollectionDataProvider;
use Owl\Shared\Infrastructure\DataProvider\Orm\Factory\QueryBuilderFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

class CollectionDataProviderTest extends TestCase
{
    use ProphecyTrait;

    public function testGetCollection(): void
    {
        $queryBuilderFactoryProphecy = $this->prophesize(QueryBuilderFactoryInterface::class);
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class)->reveal();
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class)->reveal();

        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->willReturn([])->shouldBeCalled();

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->getQuery()->willReturn($queryProphecy->reveal());
        $queryBuilder = $queryBuilderProphecy->reveal();

        $queryBuilderFactoryProphecy->create('DataClass', $collectionTypeProphecy)->willReturn($queryBuilder);

        // create prophecies for the applicators
        $applicatorProphcy = $this->prophesize(CollectionApplicatorInterface::class);
        $applicatorProphcy->applyToCollection($queryBuilder, $collectionTypeProphecy, $collectionRequestParamsProphecy)->shouldBeCalled();

        // create the object under test
        $collectionDataProvider = new CollectionDataProvider($queryBuilderFactoryProphecy->reveal(), [$applicatorProphcy->reveal()]);

        // call the method being tested
        $result = $collectionDataProvider->get('DataClass', $collectionTypeProphecy, $collectionRequestParamsProphecy);

        // assert that the result is the expected value
        $this->assertEquals([], $result);
    }

    public function testApplicatorWithBuilder(): void
    {
        $queryBuilderFactoryProphecy = $this->prophesize(QueryBuilderFactoryInterface::class);
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class)->reveal();
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class)->reveal();

        $queryProphecy = $this->prophesize(AbstractQuery::class);
        $queryProphecy->getResult()->willReturn([])->shouldBeCalled();

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->getQuery()->willReturn($queryProphecy->reveal());
        $queryBuilder = $queryBuilderProphecy->reveal();

        $queryBuilderFactoryProphecy->create('DataClass', $collectionTypeProphecy)->willReturn($queryBuilder);

        // create prophecies for the applicators
        $applicatorProphcy = $this->prophesize(CollectionApplicatorInterface::class);
        $applicatorProphcy->willImplement(BuilderAwareInterface::class);
        $applicatorProphcy->setBuilder(Argument::type(BuilderRegistryInterface::class), $collectionTypeProphecy, $collectionRequestParamsProphecy)->shouldBeCalled();
        $applicatorProphcy->applyToCollection($queryBuilder, $collectionTypeProphecy, $collectionRequestParamsProphecy)->shouldBeCalled();

        // create the object under test
        $collectionDataProvider = new CollectionDataProvider($queryBuilderFactoryProphecy->reveal(), [$applicatorProphcy->reveal()]);

        // call the method being tested
        $result = $collectionDataProvider->get('DataClass', $collectionTypeProphecy, $collectionRequestParamsProphecy);

        // // assert that the result is the expected value
        $this->assertEquals([], $result);
    }

    public function testResultableApplicator(): void
    {
        $queryBuilderFactoryProphecy = $this->prophesize(QueryBuilderFactoryInterface::class);
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class)->reveal();
        $collectionRequestParamsProphecy = $this->prophesize(CollectionRequestParamsInterface::class)->reveal();

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilder = $queryBuilderProphecy->reveal();

        $queryBuilderFactoryProphecy->create('DataClass', $collectionTypeProphecy)->willReturn($queryBuilder);

        // create prophecies for the applicators
        $applicatorProphcy = $this->prophesize(CollectionResultableApplicatorInterface::class);
        $applicatorProphcy->willImplement(BuilderAwareInterface::class);
        $applicatorProphcy->setBuilder(Argument::type(BuilderRegistryInterface::class), $collectionTypeProphecy, $collectionRequestParamsProphecy)->shouldBeCalled();
        $applicatorProphcy->applyToCollection($queryBuilder, $collectionTypeProphecy, $collectionRequestParamsProphecy)->shouldBeCalled();
        $applicatorProphcy->supportsResult($collectionTypeProphecy, $collectionRequestParamsProphecy, Argument::type(BuilderRegistryInterface::class))->willReturn(true)->shouldBeCalled();
        $applicatorProphcy->getResult($queryBuilder, $collectionTypeProphecy, $collectionRequestParamsProphecy, Argument::type(BuilderRegistryInterface::class))->willReturn([])->shouldBeCalled();

        // create the object under test
        $collectionDataProvider = new CollectionDataProvider($queryBuilderFactoryProphecy->reveal(), [$applicatorProphcy->reveal()]);

        // call the method being tested
        $result = $collectionDataProvider->get('DataClass', $collectionTypeProphecy, $collectionRequestParamsProphecy);

        // // assert that the result is the expected value
        $this->assertEquals([], $result);
    }
}
