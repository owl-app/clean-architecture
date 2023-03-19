<?php

declare(strict_types=1);

namespace Owl\Apps\Api\Controller\Article;

use Owl\Article\Application\Create\CreateArticleCommand;
use Owl\Article\Application\Dto\CreateArticleRequestDto;
use Owl\Shared\Infrastructure\Symfony\ApiController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ArticlePostController extends ApiController
{
    public function __invoke(CreateArticleRequestDto $createArticleRequestDto, Request $request): JsonResponse
    {
        $payload = json_decode($request->getContent(), true);

        $this->dispatch(
            new CreateArticleCommand(
                (string) $payload['title'],
                (string) $payload['description']
            )
        );

        return new JsonResponse(
            ['test' => 'test']
        );
    }
}
