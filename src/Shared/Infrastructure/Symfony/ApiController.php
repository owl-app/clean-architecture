<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony;

use Owl\Shared\Domain\Bus\Command\CommandBusInterface;
use Owl\Shared\Domain\Bus\Command\CommandInterface;
use Owl\Shared\Domain\Bus\Query\QueryBusInterface;
use Owl\Shared\Domain\Bus\Query\QueryInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

abstract class ApiController
{
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly QueryBusInterface $queryBus,
        private readonly SerializerInterface $serializer
    ) {

    }

    protected function dispatch(CommandInterface $command): void
    {
        $this->commandBus->dispatch($command);
    }

    protected function query(QueryInterface $query): mixed
    {
        return $this->queryBus->query($query);
    }

    protected function responseJson(mixed $data): JsonResponse
    {
        return (new JsonResponse)::fromJsonString($this->serializer->serialize($data, 'json', [
                AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
                // AbstractObjectNormalizer::ATTRIBUTES => ['data' => ['id', 'title'], 'metadata' => ['currentPage']]
            ])
        );
    }
}
