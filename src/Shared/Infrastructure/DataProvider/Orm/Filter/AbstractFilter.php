<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\DataProvider\Orm\Filter;

use Owl\Shared\Domain\DataProvider\Filter\AbstractFilter as DomainAbstractFilter;

abstract class AbstractFilter extends DomainAbstractFilter implements FilterInterface
{
}
