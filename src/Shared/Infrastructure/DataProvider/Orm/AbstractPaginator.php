<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use IteratorAggregate;
use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;
use Owl\Shared\Domain\DataProvider\Pagination\PartialPaginatorInterface;
use ReturnTypeWillChange;
use Traversable;

/**
 * @implements IteratorAggregate<array-key, mixed>
 */
abstract class AbstractPaginator implements IteratorAggregate, PartialPaginatorInterface
{
    protected DoctrinePaginator $paginator;

    protected ?Traversable $iterator = null;

    protected ?int $firstResult;

    protected ?int $maxResults;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(DoctrinePaginator $paginator)
    {
        $query = $paginator->getQuery();

        if (null === ($firstResult = $query->getFirstResult()) || null === $maxResults = $query->getMaxResults()) {
            throw new InvalidArgumentException(sprintf('"%1$s::setFirstResult()" or/and "%1$s::setMaxResults()" was/were not applied to the query.', Query::class));
        }

        $this->paginator = $paginator;
        $this->firstResult = $firstResult;
        $this->maxResults = $maxResults;
    }

    /**
     * @inheritdoc
     */
    public function getCurrentPage(): float
    {
        if (0 >= $this->maxResults) {
            return 1.;
        }

        return floor($this->firstResult / $this->maxResults) + 1.;
    }

    /**
     * @inheritdoc
     */
    public function getItemsPerPage(): float
    {
        return (float) $this->maxResults;
    }

    /**
     * @inheritdoc
     *
     * @psalm-return Traversable<array-key, mixed>
     */
    #[ReturnTypeWillChange]
    public function getIterator(): Traversable
    {
        if (null === $this->iterator) {
            $this->iterator = $this->paginator->getIterator();
        }

        return $this->iterator;
    }

    /**
     * @inheritdoc
     */
    #[ReturnTypeWillChange]
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }
}
