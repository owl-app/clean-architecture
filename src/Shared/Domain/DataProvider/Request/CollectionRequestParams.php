<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Request;

class CollectionRequestParams extends RequestParams implements CollectionRequestParamsInterface
{
    public function getDefaultFiltering(): array
    {
        if (isset($this->parameters['filtering'])) {
            return $this->parameters['filtering'];
        }

        return [];
    }

    public function getDefaultSorting(): array
    {
        if (isset($this->parameters['sorting'])) {
            return $this->parameters['sorting'];
        }

        return [];
    }

    public function getDefaultPagination(): array
    {
        if (isset($this->parameters['pagination'])) {
            return $this->parameters['pagination'];
        }

        return [];
    }
}
