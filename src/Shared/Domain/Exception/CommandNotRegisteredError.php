<?php

declare(strict_types=1);

namespace Owl\Shared\Domain\Exception;

use Owl\Shared\Domain\Bus\Command\CommandInterface;
use RuntimeException;

final class CommandNotRegisteredError extends RuntimeException
{
    public function __construct(CommandInterface $command)
    {
        $commandClass = $command::class;

        parent::__construct("The command <$commandClass> hasn't a command handler associated");
    }
}
