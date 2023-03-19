<?php

declare(strict_types=1);

namespace Owl\Article\Infrastructure\Persistence;

use Owl\Article\Domain\Model\Article;
use Owl\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use Owl\Article\Domain\Repository\ArticleRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

final class ArticleRepository extends DoctrineRepository implements ArticleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    public function save(Article $article): void
    {
        $this->persist($article);
    }
}
