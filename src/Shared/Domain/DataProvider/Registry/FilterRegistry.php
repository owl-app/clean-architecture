<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Registry;

use Owl\Shared\Domain\DataProvider\Exception\ExistingServiceException;
use Owl\Shared\Domain\DataProvider\Exception\NonExistingServiceException;

/**
 * Cannot be final, because it is proxied
 */
class FilterRegistry implements FilterRegistryInterface
{
    /**
     * @psalm-var array<string, object>
     *
     * @var object[]
     */
    private $services = [];

    /**
     * Interface or parent class which is required by all services.
     *
     * @var string
     */
    private $className;

    /**
     * Human readable context for these services, e.g. "grid field"
     *
     * @var string
     */
    private $context;

    public function __construct(string $className, string $context = 'filter')
    {
        $this->className = $className;
        $this->context = $context;
    }

    public function all(): array
    {
        return $this->services;
    }

    public function register(string $identifier, $service): void
    {
        if ($this->has($identifier)) {
            throw new ExistingServiceException($this->context, $identifier);
        }

        if (!$service instanceof $this->className) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to be of type "%s", "%s" given.', ucfirst($this->context), $this->className, get_class($service)),
            );
        }

        $this->services[$identifier] = $service;
    }

    public function unregister(string $identifier): void
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        unset($this->services[$identifier]);
    }

    public function has(string $identifier): bool
    {
        return isset($this->services[$identifier]);
    }

    public function get(string $identifier): object
    {
        if (!$this->has($identifier)) {
            throw new NonExistingServiceException($this->context, $identifier, array_keys($this->services));
        }

        return $this->services[$identifier];
    }
}
