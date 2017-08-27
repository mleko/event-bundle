<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Tests\DependencyInjection\Compiler\Listener;

use Mleko\Narrator\Bundle\DependencyInjection\Compiler\UnknownEventName;
use Mleko\Narrator\Bundle\Tests\Integration\TestApp\Counter;
use Mleko\Narrator\Bundle\Tests\Integration\TestApp\StdCounter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class ListenerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $container;

    /** @var \Mleko\Narrator\Bundle\DependencyInjection\Compiler\ListenerPass */
    private $compilerPass;

    /** @var Definition */
    private $defaultEmitterDefinition;

    public function setUp()
    {
        $this->container = $this->getMockBuilder(ContainerBuilder::class)->getMock();
        $this->container = new ContainerBuilder();

        $this->defaultEmitterDefinition = new Definition(null, [null, []]);

        $this->container->setDefinition('narrator.event_bus.default', $this->defaultEmitterDefinition);

        $this->compilerPass = new \Mleko\Narrator\Bundle\DependencyInjection\Compiler\ListenerPass();
    }

    public function testProcess()
    {
        $listenerDefinition = new Definition();
        $listenerDefinition->addTag("narrator.listener", ["event" => \stdClass::class]);
        $this->container->setDefinition("service1", $listenerDefinition);

        $this->compilerPass->process($this->container);

        $listeners = $this->defaultEmitterDefinition->getArgument(1);
        $this->assertCount(1, $listeners[\stdClass::class]);
    }

    public function testFailOnMissingEventName()
    {
        $listenerDefinition = new Definition();
        $listenerDefinition->addTag("narrator.listener");
        $this->container->setDefinition("service1", $listenerDefinition);

        $this->expectException(UnknownEventName::class);

        $this->compilerPass->process($this->container);
    }

    public function testEventClassInferring()
    {
        $counterDefinition = new Definition(StdCounter::class);
        $counterDefinition->addTag("narrator.listener");
        $this->container->setDefinition("counter", $counterDefinition);

        $this->compilerPass->process($this->container);

        $listeners = $this->defaultEmitterDefinition->getArgument(1);
        $this->assertCount(1, $listeners[\stdClass::class]);
    }

    public function testUntypedEventHandlerClassInferring()
    {
        $counterDefinition = new Definition(Counter::class);
        $counterDefinition->addTag("narrator.listener");
        $this->container->setDefinition("counter", $counterDefinition);

        $this->expectException(UnknownEventName::class);

        $this->compilerPass->process($this->container);
    }

    public function testParameterLessEventHandlerClassInferring()
    {
        $counterDefinition = new Definition(StdCounter::class);
        $counterDefinition->addTag("narrator.listener", ["method" => "handleParameterLess"]);
        $this->container->setDefinition("counter", $counterDefinition);

        $this->expectException(UnknownEventName::class);

        $this->compilerPass->process($this->container);
    }
}
