<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Pagination;

/**
 * @extends \Traversable<mixed>
 */
interface PartialPaginatorInterface extends \Traversable, \Countable
{
    public function getCurrentPage(): float;

    public function getItemsPerPage(): float;
}
