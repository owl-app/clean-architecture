<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create;

use Owl\Shared\Domain\Bus\Command\CommandHandlerInterface;

final class CreateArticleCommandHandler implements CommandHandlerInterface
{
    public function __construct(private readonly ArticleCreator $creator)
    {
    }

    public function __invoke(CreateArticleCommand $command): void
    {
        $this->creator->__invoke($command->title(), $command->description());
    }
}
