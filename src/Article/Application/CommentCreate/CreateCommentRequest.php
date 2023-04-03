<?php

declare(strict_types=1);

namespace Owl\Article\Application\CommentCreate;

use Owl\Shared\Application\Dto\RequestDtoInterface;

class CreateCommentRequest implements RequestDtoInterface
{
    public function __construct(private string $description)
    {
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
