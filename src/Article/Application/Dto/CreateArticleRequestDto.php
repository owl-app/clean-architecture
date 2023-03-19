<?php

declare(strict_types=1);

namespace Owl\Article\Application\Dto;

use Owl\Shared\Application\Dto\RequestDtoInterface;

class CreateArticleRequestDto implements RequestDtoInterface
{
    public string $name;

    public int $position;

    public $title;

    /** @var CreateCommentRequestDto[] */
    public $comment;
}
