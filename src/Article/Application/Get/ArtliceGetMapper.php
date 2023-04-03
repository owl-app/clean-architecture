<?php

declare(strict_types=1);

namespace Owl\Article\Application\Get;

use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\DataProvider\Mapper\ItemMapperInterface;

final class ArtliceGetMapper implements ItemMapperInterface
{
    /**
     * @param Article|object $data
     *
     * @return ArticleResponse
     */
    public function toResponse(object $data): object
    {
        return new ArticleResponse(
            $data->getId(),
            $data->getTitle(),
        );
    }
}
