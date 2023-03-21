<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Owl\Article\Application\List\ArticleGet;
use Owl\Article\Domain\Model\Article;
use Owl\Shared\Domain\DataProvider\Request\RequestParams;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArticleGetController extends ApiController
{
    #[OA\Response(
        response: 200,
        description: 'Successful response',
        content: new OA\JsonContent(
            ref: new Model(type: Article::class)
        )
    )]
    #[OA\Parameter(
        name: "id",
        in: "query",
        description: "Id article",
        required: true,
    )]
    #[OA\Tag(name: 'Articles', description: 'Articles in system')]
    public function __invoke(RequestParams $requestParams, ArticleGet $articleGet): JsonResponse
    {
        return $this->responseJson($articleGet->__invoke($requestParams));
    }
}
