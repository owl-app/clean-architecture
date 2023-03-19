<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Symfony\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RegisterDataProviderFilterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('owl.registry.date_provider_filter')) {
            return;
        }

        $registry = $container->getDefinition('owl.registry.date_provider_filter');

        foreach ($container->findTaggedServiceIds('owl.data_provider_filter') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            $registry->addMethodCall('register', [$serviceDefinition->getClass(), new Reference($serviceId)]);
        }
    }
}
