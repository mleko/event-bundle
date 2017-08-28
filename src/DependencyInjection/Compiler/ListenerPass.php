<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\DependencyInjection\Compiler;


use Mleko\Narrator\Bundle\Listener\ListenerService;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ListenerPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('narrator.listener');
        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $this->processServiceTag($container, $tag, $serviceId);
            }
        }
    }

    /**
     * @param ContainerBuilder $container
     * @param array $tag
     * @param string $serviceId
     */
    private function processServiceTag(ContainerBuilder $container, $tag, $serviceId)
    {
        $methodName = isset($tag['method']) ? $tag['method'] : "handle";
        $eventName = $this->extractEventName($container, $tag, $serviceId, $methodName);
        if (null === $eventName) {
            throw new UnknownEventName($serviceId);
        }

        $busName = isset($tag['bus']) ? $tag['bus'] : 'default';
        $this->registerListener($container, $eventName, $busName, $serviceId, $methodName);
    }

    private function registerListener(ContainerBuilder $container, $eventName, $emitterName, $listenerServiceId, $methodName)
    {
        $emitterDefinition = $container->getDefinition('narrator.event_bus.' . $emitterName);
        $listeners = $emitterDefinition->getArgument(1);
        $listeners[$eventName][] = new Definition(
            ListenerService::class,
            [
                $listenerServiceId,
                new Reference("service_container"),
                $methodName
            ]
        );
        $emitterDefinition->replaceArgument(1, $listeners);
    }

    private function extractEventName($container, $tag, $serviceId, $methodName)
    {
        if (!isset($tag['event'])) {
            return $this->inferEventName($container, $serviceId, $methodName);

        }
        return $tag['event'];
    }

    private function inferEventName(ContainerBuilder $container, $serviceId, $methodName)
    {
        $listenerClass = $container->getDefinition($serviceId)->getClass();
        if (null === $listenerClass) {
            return null;
        }
        $reflectionMethod = new \ReflectionMethod($listenerClass, $methodName);
        $parameters = $reflectionMethod->getParameters();
        if (count($parameters) < 1) {
            return null;
        }
        $firstParameter = $parameters[0];
        $firstParameterType = $firstParameter->getClass();
        if (null === $firstParameterType) {
            return null;
        }
        return $firstParameterType->getName();
    }
}
