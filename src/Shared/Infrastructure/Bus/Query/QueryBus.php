<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Bus\Query;

use Owl\Shared\Domain\Bus\Query\QueryBusInterface;
use Owl\Shared\Domain\Bus\Query\QueryInterface;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class QueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(
        private MessageBusInterface $messageBus,
    ) {
    }

    public function query(QueryInterface $query): mixed
    {
        return $this->handle($query);
    }
}
