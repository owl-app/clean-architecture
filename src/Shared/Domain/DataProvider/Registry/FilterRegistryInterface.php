<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Registry;

use Owl\Shared\Domain\DataProvider\Exception\ExistingServiceException;
use Owl\Shared\Domain\DataProvider\Exception\NonExistingServiceException;

interface FilterRegistryInterface
{
    public function all(): array;

    /**
     * @param object $service
     *
     * @throws ExistingServiceException
     * @throws \InvalidArgumentException
     */
    public function register(string $identifier, $service): void;

    /**
     * @throws NonExistingServiceException
     */
    public function unregister(string $identifier): void;

    public function has(string $identifier): bool;

    /**
     * @return object
     *
     * @throws NonExistingServiceException
     */
    public function get(string $identifier);
}
