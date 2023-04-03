<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use OpenApi\Attributes as OA;
use Owl\Article\Application\Create\ArticleCreator;
use Owl\Article\Application\Create\Command\SendEmailNewArticle;
use Owl\Article\Application\Create\CreateArticleRequest;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ArticlePostController extends ApiController
{
    #[OA\Post(
        summary: 'Update article',
        requestBody: new OA\RequestBody(
            content: new OA\JsonContent(
                required: ['title', 'description'],
                properties: [
                    new OA\Property(
                        property: 'title',
                        type: 'string',
                        description: 'Article title',
                        format: 'text',
                    ),
                    new OA\Property(
                        property: 'description',
                        type: 'string',
                        description: 'Article description',
                        format: 'text',
                    ),
                ],
            ),
        ),
    )]
    #[OA\Response(
        response: 200,
        description: 'Successful response',
    )]
    #[OA\Tag(name: 'Articles', description: 'Articles in system')]
    public function __invoke(CreateArticleRequest $createArticleRequest, ArticleCreator $articleCreator): JsonResponse
    {
        $article = $articleCreator->__invoke($createArticleRequest);

        $this->dispatch(
            new SendEmailNewArticle(
                $createArticleRequest->getTitle(),
                $createArticleRequest->getDescription(),
            ),
        );

        return $this->responseCreated($article);
    }
}
