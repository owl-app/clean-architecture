<?php

declare(strict_types=1);

namespace Owl\Article\Application\List;

use Owl\Article\Domain\Model\Article;
use Owl\Article\Infrastructure\DataProvider\ArticleItemDataProvider;
use Owl\Shared\Domain\DataProvider\ItemDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;

final class ArticleGet
{
    public function __construct(
        private readonly ItemDataProviderInterface $itemDataProvider
    ) {
    }

    public function __invoke(RequestParamsInterface $requestParams): Article
    {
        /** @var Article $data */
        $data = $this->itemDataProvider->get(Article::class, $requestParams, new ArticleItemDataProvider());

        return $data;
    }
}
