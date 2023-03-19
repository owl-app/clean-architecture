<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Bus\Command;

use Owl\Shared\Domain\Bus\Command\CommandBusInterface;
use Owl\Shared\Domain\Bus\Command\CommandInterface;
use Owl\Shared\Domain\Exception\CommandNotRegisteredError;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Exception\NoHandlerForMessageException;
use Symfony\Component\Messenger\MessageBusInterface;

final class CommandBus implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    public function dispatch(CommandInterface $command): void
    {
        try {
            $this->messageBus->dispatch($command);
        } catch (NoHandlerForMessageException) {
            throw new CommandNotRegisteredError($command);
        } catch (HandlerFailedException $error) {
            throw $error->getPrevious() ?? $error;
        }
    }
}
