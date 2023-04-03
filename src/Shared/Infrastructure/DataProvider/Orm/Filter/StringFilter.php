<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Filter;

use Doctrine\ORM\QueryBuilder;
use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;
use Owl\Shared\Domain\DataProvider\Util\QueryNameGeneratorInterface;

final class StringFilter extends AbstractFilter
{
    public const NAME = 'string';

    public const TYPE_EQUAL = 'equal';

    public const TYPE_NOT_EQUAL = 'not_equal';

    public const TYPE_EMPTY = 'empty';

    public const TYPE_NOT_EMPTY = 'not_empty';

    public const TYPE_CONTAINS = 'contains';

    public const TYPE_NOT_CONTAINS = 'not_contains';

    public const TYPE_STARTS_WITH = 'starts_with';

    public const TYPE_ENDS_WITH = 'ends_with';

    public const TYPE_MEMBER_OF = 'member_of';

    public const TYPE_IN = 'in';

    public const TYPE_NOT_IN = 'not_in';

    public function buildQuery(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, mixed $data, array $fieldAliases, array $options): void
    {
        $value = is_array($data) ? $data['value'] ?? null : $data;
        $type = $data['type'] ?? ($options['type'] ?? self::TYPE_CONTAINS);

        if (1 === count($fieldAliases)) {
            $queryBuilder->andWhere($this->getExpression($queryBuilder, $queryNameGenerator, $type, current($fieldAliases), $value));

            return;
        }

        $expressions = [];
        foreach ($fieldAliases as $field) {
            $expressions[] = $this->getExpression($queryBuilder, $queryNameGenerator, $type, $field, $value);
        }

        if (self::TYPE_NOT_EQUAL === $type) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->andX(...$expressions),
            );

            return;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(...$expressions),
        );
    }

    private function getExpression(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $type,
        string $field,
        mixed $value,
    ): mixed {
        switch ($type) {
            case self::TYPE_EQUAL:
                $parameterName = $queryNameGenerator->generateParameterName($field);
                $queryBuilder->setParameter($parameterName, $value);

                return $queryBuilder->expr()->eq($field, ':' . $parameterName);
            case self::TYPE_NOT_EQUAL:
                $parameterName = $queryNameGenerator->generateParameterName($field);
                $queryBuilder->setParameter($parameterName, $value);

                return $queryBuilder->expr()->neq($field, ':' . $parameterName);
            case self::TYPE_EMPTY:
                return $queryBuilder->expr()->isNull($field);
            case self::TYPE_NOT_EMPTY:
                return $queryBuilder->expr()->isNotNull($field);
            case self::TYPE_CONTAINS:
                return $queryBuilder->expr()->like(
                    (string) $queryBuilder->expr()->lower($field),
                    $queryBuilder->expr()->literal(strtolower('%' . $value . '%')),
                );
            case self::TYPE_NOT_CONTAINS:
                return $queryBuilder->expr()->notLike(
                    (string) $queryBuilder->expr()->lower($field),
                    $queryBuilder->expr()->literal(strtolower('%' . $value . '%')),
                );
            case self::TYPE_STARTS_WITH:
                return $queryBuilder->expr()->like(
                    (string) $queryBuilder->expr()->lower($field),
                    $queryBuilder->expr()->literal(strtolower($value . '%')),
                );
            case self::TYPE_ENDS_WITH:
                return $queryBuilder->expr()->like(
                    (string) $queryBuilder->expr()->lower($field),
                    $queryBuilder->expr()->literal(strtolower('%' . $value)),
                );
            case self::TYPE_IN:
                return $queryBuilder->expr()->in($field, array_map('trim', explode(',', $value)));
            case self::TYPE_NOT_IN:
                return $queryBuilder->expr()->notIn($field, array_map('trim', explode(',', $value)));
            case self::TYPE_MEMBER_OF:
                return $queryBuilder->expr()->isMemberOf($value, $field);
            default:
                throw new InvalidArgumentException(sprintf('Could not get an expression for type "%s"!', $type));
        }
    }
}
