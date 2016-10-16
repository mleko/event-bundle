<?php
/**
 * @package narrator-bundle
 */


namespace Mleko\Narrator\Bundle;


class EmitterFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    /**
     * EmitterFactory constructor.
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Mleko\Narrator\EventNameExtractor $nameExtractor
     * @param $listenerDefinitions
     * @return \Mleko\Narrator\SimpleEmitter
     */
    public function createEmitter(\Mleko\Narrator\EventNameExtractor $nameExtractor, $listenerDefinitions)
    {
        $listeners = [];
        foreach ($listenerDefinitions as $eventName => $listenerDefinition) {
            foreach ($listenerDefinition as $listenerData) {
                $listeners[$eventName][] = new Listener\ListenerService($listenerData['serviceId'], $this->container, $listenerData['methodName']);
            }
        }
        return new \Mleko\Narrator\SimpleEmitter($nameExtractor, $listeners);
    }

}
