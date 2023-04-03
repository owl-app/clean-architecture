<?php

declare(strict_types=1);

namespace Owl\Article\Domain\Model;

use Owl\Shared\Domain\Persistence\AbstractBaseEntity;

final class Article extends AbstractBaseEntity
{
    public function __construct(
        private string $title,
        private string $description,
    ) {
    }

    public static function create(string $title, string $description): self
    {
        $article = new self($title, $description);

        return $article;
    }

    public function toPrimitives(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
