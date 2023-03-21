<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Request;

class RequestParams implements RequestParamsInterface
{
    protected array $parameters;

    protected array $query;

    public function __construct(array $parameters, array $query)
    {
        $this->parameters = $parameters;
        $this->query = $query;
    }

    public function getQueryParams(): array
    {
        return $this->query;
    }
}
