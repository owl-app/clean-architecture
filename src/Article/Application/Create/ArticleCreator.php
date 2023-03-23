<?php

declare(strict_types=1);

namespace Owl\Article\Application\Create;

use Owl\Article\Domain\Model\Article;
use Owl\Article\Domain\Repository\ArticleRepositoryInterface;

final class ArticleCreator
{
    public function __construct(
        private readonly ArticleRepositoryInterface $repository
    ) {
    }

    public function __invoke(CreateArticleRequest $createArticleRequest): Article
    {
        $article = Article::create($createArticleRequest->getTitle(), $createArticleRequest->getDescription());

        $this->repository->save($article);

        return $article;
    }
}
