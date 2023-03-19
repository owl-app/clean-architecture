<?php

declare(strict_types=1);

namespace Owl\Article\Domain\Repository;

use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\Persistence\RepositoryInterface;

interface ArticleRepositoryInterface extends RepositoryInterface
{
    public function save(Article $article): void;
}
