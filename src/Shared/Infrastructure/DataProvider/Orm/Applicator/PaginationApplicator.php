<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrineOrmPaginator;
use Owl\Shared\Domain\DataProvider\Builder\BuilderAwareInterface;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilder;
use Owl\Shared\Domain\DataProvider\Builder\PaginationBuilderInterface;
use Owl\Shared\Domain\DataProvider\Registry\BuilderRegistryInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\CollectionTypeInterface;
use Owl\Shared\Domain\DataProvider\Validation\PaginationParametersValidator;
use Owl\Shared\Domain\DataProvider\Validation\PaginationParametersValidatorInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Paginator;

class PaginationApplicator implements CollectionResultableApplicatorInterface, BuilderAwareInterface
{
    private PaginationBuilderInterface $builder;

    private PaginationParametersValidatorInterface $paginationValidator;

    public function __construct(?PaginationParametersValidatorInterface $paginationValidator = null)
    {
        $this->paginationValidator = $paginationValidator ?? new PaginationParametersValidator();
    }

    public function setBuilder(BuilderRegistryInterface $builderRegistry, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $this->builder = new PaginationBuilder($collectionRequestParams->getDefaultPagination(), $collectionRequestParams->getQueryParams());
        $builderRegistry->add($this->builder->getName(), $this->builder);
    }

    public function applyToCollection(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams): void
    {
        $collectionType->buildPagination($this->builder);

        if (!$this->builder->hasPagination()) {
            return;
        }

        $offset = $this->builder->getOffset();
        $limit = $this->builder->getPerPage();
        $page = $this->builder->getPage();
        $allowedPerPage = $this->builder->getAllowedPerPage();

        $this->paginationValidator->validatePaginationParameters($offset, $limit, $page, $allowedPerPage);

        $queryBuilder
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;
    }

    public function supportsResult(CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams, BuilderRegistryInterface $builderRegistry): bool
    {
        return $this->builder->hasPagination();
    }

    public function getResult(QueryBuilder $queryBuilder, CollectionTypeInterface $collectionType, CollectionRequestParamsInterface $collectionRequestParams, BuilderRegistryInterface $builderRegistry): iterable
    {
        $query = $queryBuilder->getQuery();

        // Only one alias, without joins, disable the DISTINCT on the COUNT
        if (1 === \count($queryBuilder->getAllAliases())) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $doctrineOrmPaginator = new DoctrineOrmPaginator($query, $this->builder->getFetchJoinCollection());
        $doctrineOrmPaginator->setUseOutputWalkers($this->builder->getUseOutputWalkers());

        return new Paginator($doctrineOrmPaginator);
    }
}
