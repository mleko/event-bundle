<?php
/**
 * @package event-bundle
 */


namespace Mleko\Event\Bundle;


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
     * @param \Mleko\Event\EventNameExtractor $nameExtractor
     * @param $listenerDefinitions
     * @return \Mleko\Event\SimpleEmitter
     */
    public function createEmitter(\Mleko\Event\EventNameExtractor $nameExtractor, $listenerDefinitions)
    {
        $listeners = [];
        foreach ($listenerDefinitions as $eventName => $listenerDefinition) {
            foreach ($listenerDefinition as $listenerData) {
                $listeners[$eventName][] = new \Mleko\Event\Bundle\Listener\ListenerService($listenerData['serviceId'], $this->container, $listenerData['methodName']);
            }
        }
        return new \Mleko\Event\SimpleEmitter($nameExtractor, $listeners);
    }

}
