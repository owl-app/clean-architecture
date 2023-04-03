<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends HttpException
{
    public function __construct(private ConstraintViolationListInterface $violationList)
    {
        parent::__construct(Response::HTTP_UNPROCESSABLE_ENTITY, 'Request validation failed');
    }

    public function getViolationList(): ConstraintViolationListInterface
    {
        return $this->violationList;
    }
}
