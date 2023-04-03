<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Resolver;

use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Exception\RuntimeException;

class FieldResolver implements FieldResolverInterface
{
    public function resolveFieldByAddingJoins(QueryBuilder $queryBuilder, string $field): string
    {
        [$field, $className] = $this->getFieldDetails($queryBuilder, $field);
        $metadata = $queryBuilder->getEntityManager()->getClassMetadata($className);

        while (count($explodedField = explode('.', $field, 3)) === 3) {
            [$rootField, $associationField, $remainder] = $explodedField;

            if (isset($metadata->embeddedClasses[$associationField])) {
                break;
            }

            $metadata = $queryBuilder->getEntityManager()->getClassMetadata(
                $metadata->getAssociationMapping($associationField)['targetEntity'],
            );
            $rootAndAssociationField = sprintf('%s.%s', $rootField, $associationField);

            /** @var Join[] $joins */
            $joins = array_merge([], ...array_values($queryBuilder->getDQLPart('join')));
            foreach ($joins as $join) {
                if ($join->getJoin() === $rootAndAssociationField) {
                    $field = sprintf('%s.%s', (string) $join->getAlias(), $remainder);

                    continue 2;
                }
            }

            // Association alias can't start with a number
            // Mapping numbers to letters will not increase the collision probability and not lower the entropy
            $associationAlias = str_replace(
                ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
                ['g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p'],
                md5($rootAndAssociationField),
            );

            $queryBuilder->innerJoin($rootAndAssociationField, $associationAlias);
            $field = sprintf('%s.%s', $associationAlias, $remainder);
        }

        return $field;
    }

    private function getFieldDetails(QueryBuilder $queryBuilder, string $field): array
    {
        $rootField = explode('.', $field)[0];
        if (!in_array($rootField, $queryBuilder->getAllAliases(), true)) {
            $field = sprintf('%s.%s', $queryBuilder->getRootAliases()[0], $field);
        }

        /** @var Join[] $joins */
        $joins = array_merge([], ...array_values($queryBuilder->getDQLPart('join')));
        while ($explodedField = explode('.', $field, 2)) {
            $rootField = $explodedField[0];
            $remainder = $explodedField[1] ?? '';

            if (in_array($rootField, $queryBuilder->getRootAliases(), true)) {
                break;
            }

            foreach ($joins as $join) {
                if ($join->getAlias() === $rootField) {
                    $joinSubject = $join->getJoin();

                    if (class_exists($joinSubject)) {
                        return [$field, $joinSubject];
                    }

                    $field = rtrim(sprintf('%s.%s', $joinSubject, $remainder), '.');

                    continue 2;
                }
            }

            throw new RuntimeException(sprintf('Could not get mapping for "%s".', $field));
        }

        /** @var From[] $froms */
        $froms = $queryBuilder->getDQLPart('from');
        foreach ($froms as $from) {
            if ($from->getAlias() === $rootField) {
                return [$field, $from->getFrom()];
            }
        }

        throw new RuntimeException(sprintf('Could not get metadata for "%s".', $rootField));
    }
}
