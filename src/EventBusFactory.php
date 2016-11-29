<?php
/**
 * @package narrator-bundle
 */


namespace Mleko\Narrator\Bundle;


use Mleko\Narrator\EventNameExtractor;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventBusFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * EventBusFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param EventNameExtractor $nameExtractor
     * @param $listenerDefinitions
     * @return \Mleko\Narrator\BasicEventBus
     */
    public function createEventBus(EventNameExtractor $nameExtractor, $listenerDefinitions)
    {
        $listeners = [];
        foreach ($listenerDefinitions as $eventName => $listenerDefinition) {
            foreach ($listenerDefinition as $listenerData) {
                $listeners[$eventName][] = new Listener\ListenerService($listenerData['serviceId'], $this->container, $listenerData['methodName']);
            }
        }
        return new \Mleko\Narrator\BasicEventBus($nameExtractor, $listeners);
    }

}
