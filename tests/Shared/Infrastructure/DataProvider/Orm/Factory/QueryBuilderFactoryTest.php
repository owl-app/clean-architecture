<?php

declare(strict_types=1);

namespace Owl\Tests\Shared\Infrastructure\DataProvider\Orm\Factory;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Owl\Shared\Domain\DataProvider\Exception\RuntimeException;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Factory\QueryBuilderFactory;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;
use Owl\Tests\Fixtures\DummyDataProvider;
use Owl\Tests\Fixtures\Entity\Dummy;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class QueryBuilderFactoryTest extends TestCase
{
    use ProphecyTrait;

    public function testCreateSimple(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilder = $queryBuilderProphecy->reveal();
        $managerRegistryProphecy = $this->createManagerRegistry($queryBuilder);
        $collectionTypeProphecy = $this->prophesize(CollectionTypeInterface::class);

        $queryBuilderFactory = new QueryBuilderFactory($managerRegistryProphecy->reveal());
        $queryBuilderFactory->create(Dummy::class, $collectionTypeProphecy->reveal());
    }

    public function testCreateWithBuildable(): void
    {
        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilderProphecy->addSelect('test')->shouldBeCalled();
        $queryBuilder = $queryBuilderProphecy->reveal();
        $managerRegistryProphecy = $this->createManagerRegistry($queryBuilder);
        $collectionType = new DummyDataProvider(['query_builder' => ['with_add_select' => 'test']]);

        $queryBuilderFactory = new QueryBuilderFactory($managerRegistryProphecy->reveal());
        $queryBuilderFactory->create(Dummy::class, $collectionType);
    }

    public function testExceptionCreate(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The repository class must have a "createQueryBuilder" method.');

        $queryBuilderProphecy = $this->prophesize(QueryBuilder::class);
        $queryBuilder = $queryBuilderProphecy->reveal();

        $repositoryProphecy = $this->prophesize(ObjectRepository::class);

        $managerProphecy = $this->prophesize(ObjectManager::class);
        $managerProphecy->getRepository(Dummy::class)->willReturn($repositoryProphecy->reveal())->shouldBeCalled();

        $managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistryProphecy->getManagerForClass(Dummy::class)->willReturn($managerProphecy->reveal())->shouldBeCalled();

        $collectionTypeProphecy = $this->prophesize(BuildableQueryBuilderInterface::class);

        $collectionTypeProphecy->buildQueryBuilder($queryBuilder)->shouldNotBeCalled();

        $queryBuilderFactory = new QueryBuilderFactory($managerRegistryProphecy->reveal());
        $queryBuilderFactory->create(Dummy::class, $collectionTypeProphecy->reveal());
    }

    private function createManagerRegistry(QueryBuilder $queryBuilder): ObjectProphecy
    {
        $repositoryProphecy = $this->prophesize(EntityRepository::class);
        $repositoryProphecy->createQueryBuilder('o')->willReturn($queryBuilder)->shouldBeCalled();

        $managerProphecy = $this->prophesize(ObjectManager::class);
        $managerProphecy->getClassMetadata(Dummy::class)->willReturn(new ClassMetadata(Dummy::class));
        $managerProphecy->getRepository(Dummy::class)->willReturn($repositoryProphecy->reveal())->shouldBeCalled();

        $managerRegistryProphecy = $this->prophesize(ManagerRegistry::class);
        $managerRegistryProphecy->getManagerForClass(Dummy::class)->willReturn($managerProphecy->reveal())->shouldBeCalled();

        return $managerRegistryProphecy;
    }
}
