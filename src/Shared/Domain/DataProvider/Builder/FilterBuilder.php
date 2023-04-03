<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Builder;

use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;
use Owl\Shared\Domain\DataProvider\Filter\FilterInterface;
use Owl\Shared\Domain\DataProvider\Registry\FilterRegistryInterface;

class FilterBuilder implements FilterBuilderInterface
{
    private string $paramName;

    /**
     * The children of the form builder.
     *
     * @var FilterInterface[]
     */
    private $children = [];

    /**
     * The data of children who haven't been converted services.
     *
     * @var array
     */
    private $unresolvedChildren = [];

    public function __construct(
        private readonly FilterRegistryInterface $registry,
        private readonly array $defaultParameters,
        private readonly array $queryParams,
    ) {
        $this->paramName = $defaultParameters['param_name'] ?? 'filters';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getParamName(): string
    {
        return $this->paramName;
    }

    public function setParamName(string $paramName): self
    {
        $this->paramName = $paramName;

        return $this;
    }

    public function getDataFilters(): array
    {
        if (isset($this->queryParams[$this->paramName])) {
            return $this->queryParams[$this->paramName];
        }

        return [];
    }

    public function add(string $name = null, string $filter, string|array $fields = null, array $options = []): self
    {
        if (null === $fields) {
            $filterFields = [$name];
        } else {
            $filterFields = is_string($fields) ? [$fields] : $fields;
        }

        $this->unresolvedChildren[$name] = [$filter, $filterFields, $options];

        return $this;
    }

    public function get(string $name): FilterInterface
    {
        if (isset($this->unresolvedChildren[$name])) {
            $this->resolveChildren();
        }

        if (isset($this->children[$name])) {
            return $this->children[$name];
        }

        throw new InvalidArgumentException(sprintf('The child with the name "%s" does not exist.', $name));
    }

    public function remove(string $name): self
    {
        unset($this->unresolvedChildren[$name], $this->children[$name]);

        return $this;
    }

    public function has(string $name): bool
    {
        return isset($this->unresolvedChildren[$name]) || isset($this->children[$name]);
    }

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        $this->resolveChildren();

        return $this->children;
    }

    #[\ReturnTypeWillChange]
    public function count(): int
    {
        $this->resolveChildren();

        return \count($this->children);
    }

    public function countUnresolved(): int
    {
        return \count($this->unresolvedChildren);
    }

    /**
     * Converts all unresolved children into registered service.
     */
    private function resolveChildren(): void
    {
        if ($this->countUnresolved() > 0) {
            foreach ($this->unresolvedChildren as $name => $info) {
                $classFilter = $this->registry->get($info[0]);
                /**
                 * @var FilterInterface $filterService
                 */
                $filterService = new $classFilter();
                $filterService->setName($name);
                $filterService->setFields($info[1]);
                $filterService->setOptions($info[2]);
                $filterService->buildFilter($this);

                $this->children[$name] = $filterService;

                unset($this->unresolvedChildren[$name]);

                $this->resolveChildren();
            }
        }
    }
}
