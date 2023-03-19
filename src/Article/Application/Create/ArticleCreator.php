<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create;

use Owl\Article\Domain\Model\Article;
use Owl\Article\Domain\Repository\ArticleRepositoryInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class ArticleCreator
{
    public function __construct(
        private readonly ArticleRepositoryInterface $repository,
        private readonly MessageBusInterface $bus
    ) {
    }

    public function __invoke(string $name, string $duration): void
    {
        $article = Article::create($name, $duration);

        $this->repository->save($article);
        // $this->bus->publish(...$course->pullDomainEvents());
    }
}
