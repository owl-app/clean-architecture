<?php

declare(strict_types=1);

namespace Owl\Article\Application\List;

use IteratorAggregate;
use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\DataProvider\CollectionDataProviderInterface;
use Owl\Shared\Domain\DataProvider\Data\CollectionPaginatedData;
use Owl\Shared\Domain\DataProvider\Pagination\PaginatorInterface;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParamsInterface;

final class ArticleList
{
    public function __construct(
        private readonly CollectionDataProviderInterface $collectionDataProvider,
        private readonly ArticleCollectionDataProviderInterface $articleCollectionDataProvider
    ) {
    }

    public function __invoke(CollectionRequestParamsInterface $collectionRequestParams): CollectionPaginatedData
    {
        /** @var PaginatorInterface&IteratorAggregate $data */
        $data = $this->collectionDataProvider->get(Article::class, $this->articleCollectionDataProvider, $collectionRequestParams);

        return new CollectionPaginatedData($data);
    }
}
