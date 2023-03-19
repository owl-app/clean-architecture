<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\HttpFoundation\Response;

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
