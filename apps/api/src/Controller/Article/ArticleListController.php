<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Owl\Article\Application\List\ArtliceListMapper;
use Owl\Article\Domain\Model\Article;
use Owl\Article\Infrastructure\DataProvider\ArticleCollectionDataProvider;
use Owl\Shared\Domain\DataProvider\Request\CollectionRequestParams;
use Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query\CollectionQuery;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArticleListController extends ApiController
{
    #[OA\Get(
        summary: 'List articles',
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Article::class)),
        ),
    )]
    #[OA\Parameter(
        name: 'filters[search][type]',
        in: 'query',
        description: 'Type search',
        required: false,
        schema: new OA\Schema(
            enum: ['equal'],
        ),
    )]
    #[OA\Parameter(
        name: 'filters[search][value]',
        in: 'query',
        description: 'Value ',
        required: false,
    )]
    #[OA\Tag(name: 'Articles', description: 'Articles in system')]
    public function __invoke(CollectionRequestParams $collectionRequestParams): JsonResponse
    {
        $data = $this->query(new CollectionQuery(
            Article::class,
            new ArticleCollectionDataProvider(),
            $collectionRequestParams,
            new ArtliceListMapper(),
        ));

        return $this->responseJson($data);
    }
}
