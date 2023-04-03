<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Applicator;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Owl\Shared\Domain\DataProvider\Type\ItemTypeInterface;
use Owl\Shared\Infrastructure\DataProvider\Orm\Resolver\FieldResolverInterface;

class IdentifiersApplicator implements ItemApplicatorInterface
{
    public function __construct(private readonly FieldResolverInterface $fieldResolver)
    {
    }

    public function applyToItem(QueryBuilder $queryBuilder, ?ItemTypeInterface $itemType, RequestParamsInterface $requestParams, string $dataClass): void
    {
        $queryParams = $requestParams->getQueryParams();
        $identifiers = null !== $itemType ? $itemType->getIdentifiers() : [];

        if ($itemType || count($identifiers) === 0) {
            $classMetaData = $queryBuilder->getEntityManager()->getClassMetadata($dataClass);
            $identifiers = $classMetaData->getIdentifier();
        }

        foreach ($identifiers as $identifier => $param) {
            if (is_int($identifier)) {
                $identifier = $param;
            }

            if (isset($queryParams[$param])) {
                $field = $this->fieldResolver->resolveFieldByAddingJoins($queryBuilder, $identifier);
                $queryBuilder->andWhere($field . ' = :' . $identifier);
                $queryBuilder->setParameter(':' . $identifier, $queryParams[$identifier]);
            }
        }
    }
}
