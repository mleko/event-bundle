<?php
/**
 * @package narrator-bundle
 */


namespace Mleko\Narrator\Bundle\DependencyInjection\Compiler;


use Mleko\Narrator\Bundle\Listener\ListenerService;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ListenerPass implements \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $services = $container->findTaggedServiceIds('narrator.listener');
        foreach ($services as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $this->processServiceTag($container, $tag, $serviceId);
            }
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param array $tag
     * @param string $serviceId
     */
    private function processServiceTag(\Symfony\Component\DependencyInjection\ContainerBuilder $container, $tag, $serviceId)
    {
        if (!isset($tag['event'])) {
            throw new \RuntimeException('The narrator.listener must have event attribute');
        }
        $eventName = $tag['event'];
        $busName = isset($tag['bus']) ? $tag['bus'] : 'default';
        $methodName = isset($tag['method']) ? $tag['method'] : null;
        $this->registerListener($container, $eventName, $busName, $serviceId, $methodName);
    }

    private function registerListener(\Symfony\Component\DependencyInjection\ContainerBuilder $container, $eventName, $emitterName, $listenerServiceId, $methodName)
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
}
