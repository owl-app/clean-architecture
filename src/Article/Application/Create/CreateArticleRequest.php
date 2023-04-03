<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create;

use Owl\Article\Application\CommentCreate\CreateCommentRequest;
use Owl\Shared\Application\Dto\RequestDtoInterface;

class CreateArticleRequest implements RequestDtoInterface
{
    private string $title;

    private string $description;

    /** @var CreateCommentRequest[] */
    private $comment;

    public function __construct(string $title, string $description)
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getComment(): array
    {
        return $this->comment;
    }

    public function addComment(CreateCommentRequest $comment): void
    {
        $this->comment[] = $comment;
    }
}
