<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony\Http\ArgumentResolver;

use Owl\Shared\Application\Dto\RequestDtoInterface;
use Owl\Shared\Infrastructure\Symfony\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestDtoArgumentResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument->getType())) {
            return [];
        }

        $headers = $request->headers->all();
        /**
         * @psalm-suppress PossiblyInvalidArgument
         */
        $headers = array_combine(
            array_map(fn ($name) => str_replace('-', '_', $name), array_keys($headers)),
            array_map(fn ($value) => is_array($value) ? reset($value) : $value, $headers),
        );

        $content = $request->getContent();

        $request = $this->serializer->deserialize($content, $argument->getType(), JsonEncoder::FORMAT, [
            AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true,
        ]);

        $violations = $this->validator->validate($request);

        if ($violations->count()) {
            throw new RequestValidationException($violations);
        }

        yield $request;
    }

    private function supports(mixed $type): bool
    {
        return is_subclass_of($type, RequestDtoInterface::class);
    }
}
