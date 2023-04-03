<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\DataProvider\Data;

class CollectionData implements CollectionDataInterface
{
    public iterable $data;

    public function __construct(iterable $data)
    {
        $this->data = $data;
    }

    public function getData(): iterable
    {
        return $this->data;
    }
}
