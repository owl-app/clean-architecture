<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Owl\Article\Application\Get\ArtliceGetMapper;
use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\DataProvider\Request\RequestParams;
use Owl\Shared\Infrastructure\DataProvider\Orm\Bus\Query\ItemQuery;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArticleGetController extends ApiController
{
    #[OA\Get(
        summary: 'Article item',
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            ref: new Model(type: Article::class),
        ),
    )]
    #[OA\Tag(name: 'Articles', description: 'Articles in system')]
    public function __invoke(RequestParams $requestParams): JsonResponse
    {
        $data = $this->query(new ItemQuery(
            Article::class,
            $requestParams,
            null,
            new ArtliceGetMapper(),
        ));

        return $this->responseJson($data);
    }
}
