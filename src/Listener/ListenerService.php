<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Listener;


use Symfony\Component\DependencyInjection\ContainerInterface;

class ListenerService implements \Mleko\Narrator\Listener
{

    /** @var ContainerInterface */
    private $container;

    /** @var string */
    private $serviceId;

    /** @var string */
    private $methodName;

    /**
     * ListenerService constructor.
     * @param string $serviceId
     * @param ContainerInterface $container
     * @param string $methodName
     */
    public function __construct($serviceId, ContainerInterface $container, $methodName = 'handle')
    {
        $this->serviceId = $serviceId;
        $this->container = $container;
        $this->methodName = $methodName ?: 'handle';
    }


    /**
     * Handle an event
     *
     * @param object $event
     * @param \Mleko\Narrator\Meta $meta
     */
    public function handle($event, \Mleko\Narrator\Meta $meta)
    {
        $listener = $this->container->get($this->serviceId);
        call_user_func_array([$listener, $this->methodName], [$event, $meta]);
    }
}
