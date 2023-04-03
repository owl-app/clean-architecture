<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony\Http\ArgumentResolver;

use Owl\Shared\Domain\DataProvider\Factory\RequestParamsFactoryInterface;
use Owl\Shared\Domain\DataProvider\Request\RequestParamsInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class RequestDataProviderParamsResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly RequestParamsFactoryInterface $requestParamsFactory,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument->getType())) {
            return [];
        }

        $request = $this->requestParamsFactory->create(
            $argument->getType(),
            $request->attributes->get('data_provider', []),
            array_merge($request->query->all(), $request->attributes->get('_route_params', [])),
        );

        yield $request;
    }

    private function supports(mixed $type): bool
    {
        return is_subclass_of($type, RequestParamsInterface::class);
    }
}
