<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Tests\Listener;

class ListenerServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerInterface | \PHPUnit_Framework_MockObject_MockObject */
    private $container;

    public function setUp()
    {
        $this->container = $this->getMockForAbstractClass(\Symfony\Component\DependencyInjection\ContainerInterface::class);
    }

    public function testServiceRetrieval()
    {
        $service = $this->getMockBuilder(\stdClass::class)->setMethods(['handle'])->getMock();
        $service->expects($this->once())->method('handle');

        $this->container->expects($this->once())->method('get')->with('mock.listener')->willReturn($service);

        $listener = new \Mleko\Narrator\Bundle\Listener\ListenerService('mock.listener', $this->container);

        $event = new \stdClass();
        $meta = new \Mleko\Narrator\Meta($event, \stdClass::class, \stdClass::class, $this->getMockBuilder(\Mleko\Narrator\EventSource::class)->getMockForAbstractClass());
        $listener->handle($event, $meta);
    }
}
