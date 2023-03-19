<?php

declare(strict_types=1);

namespace Owl\Article\Application\Dto;

use Owl\Shared\Application\Dto\RequestDtoInterface;

class CreateCommentRequestDto implements RequestDtoInterface
{
    public $description;
}
