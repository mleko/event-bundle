<?php
/**
 * @package event-bundle
 */


namespace Mleko\Event\Bundle\DependencyInjection\Compiler;


class ListenerPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('mleko.event.listener');
        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!isset($tag['event'])) {
                    throw new \RuntimeException('The mleko.event.listener must have event attribute');
                }
                $eventName = $tag['event'];
                $emitterName = isset($tag['emitter']) ? $tag['emitter'] : 'default';
                $methodName = isset($tag['method']) ? $tag['method'] : null;
                $this->registerListener($container, $eventName, $emitterName, $serviceId, $methodName);
            }
        }
    }

    private function registerListener(\Symfony\Component\DependencyInjection\ContainerBuilder $container, $eventName, $emitterName, $listenerServiceId, $methodName)
    {
        $emitterDefinition = $container->getDefinition('mleko.event.emitter.' . $emitterName);
        $listeners = $emitterDefinition->getArgument(1);
        $eventListeners = isset($listeners[$eventName]) ? $listeners[$eventName] : [];
        $eventListeners[] = ['serviceId' => $listenerServiceId, 'methodName' => $methodName];
        $listeners[$eventName] = $eventListeners;
        $emitterDefinition->replaceArgument(1, $listeners);
    }
}
