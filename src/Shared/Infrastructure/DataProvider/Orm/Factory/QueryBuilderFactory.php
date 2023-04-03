<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Owl\Shared\Domain\DataProvider\Exception\RuntimeException;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Type\BuildableQueryBuilderInterface;

class QueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(private readonly ManagerRegistry $managerRegistry)
    {
    }

    public function create(string $dataClass, BuildableQueryBuilderInterface|CollectionTypeInterface|ItemTypeInterface $collectionType = null): QueryBuilder
    {
        /** @var EntityManagerInterface $manager */
        $manager = $this->managerRegistry->getManagerForClass($dataClass);

        $repository = $manager->getRepository($dataClass);
        if (!method_exists($repository, 'createQueryBuilder')) {
            throw new RuntimeException('The repository class must have a "createQueryBuilder" method.');
        }

        $queryBuilder = $repository->createQueryBuilder('o');

        if ($collectionType && $collectionType instanceof BuildableQueryBuilderInterface) {
            $collectionType->buildQueryBuilder($queryBuilder);
        }

        return $queryBuilder;
    }
}
