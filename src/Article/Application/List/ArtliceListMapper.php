<?php

declare(strict_types=1);

namespace Owl\Article\Application\List;

use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\DataProvider\Mapper\CollectionMapperInterface;
use Traversable;

final class ArtliceListMapper implements CollectionMapperInterface
{
    public function toResponse(Traversable|array $data): iterable
    {
        return array_map($this->prepareArticle(), is_array($data) ? $data : iterator_to_array($data));
    }

    private function prepareArticle(): callable
    {
        return static fn (Article $article) => new ArticleResponse(
            $article->getId(),
            $article->getTitle(),
        );
    }
}
