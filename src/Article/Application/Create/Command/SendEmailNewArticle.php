<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create\Command;

use Owl\Shared\Domain\Bus\Command\CommandInterface;

final class SendEmailNewArticle implements CommandInterface
{
    public function __construct(private readonly string $title, private readonly string $description)
    {
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }
}
