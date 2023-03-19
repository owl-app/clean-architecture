<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Factory;

use Owl\Shared\Domain\DataProvider\Exception\InvalidArgumentException;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;

final class RequestParamsFactory implements RequestParamsFactoryInterface
{
    private array $defaultParameters;

    public function __construct(array $defaultParameters = [])
    {
        $this->defaultParameters = $defaultParameters;
    }

    public function create(string $class, array $parameters, array $query): RequestParamsInterface
    {
        if (!is_subclass_of($class, RequestParamsInterface::class)) {
            throw new InvalidArgumentException(sprintf('<%s> must implements Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface', $class));
        }

        $parameters = array_merge_recursive($this->defaultParameters, $parameters);
        // $parameters = $this->parametersParser->parseRequestValues($parameters, $request);

        /** @psalm-suppress UnsafeInstantiation */
        return new $class($parameters, $query);
    }
}
