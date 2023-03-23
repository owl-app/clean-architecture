<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create\Command;

use Owl\Shared\Domain\Bus\Command\CommandHandlerInterface;

final class SendEmailNewArticleHandler implements CommandHandlerInterface
{
    public function __invoke(SendEmailNewArticle $command): void
    {
        //logic to send e-mail
    }
}
