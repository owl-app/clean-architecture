<?php

declare(strict_types=1);

namespace Owl\Article\Application\Get;

class ArticleResponse
{
    public function __construct(private readonly string $id, private readonly string $title)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
}
