<?php

/**
 * @package narrator-bundle
 */

namespace Mleko\Narrator\Bundle\Tests\DependencyInjection\Compiler\Listener;

class ListenerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Symfony\Component\DependencyInjection\ContainerBuilder | \PHPUnit_Framework_MockObject_MockObject */
    private $container;

    /** @var \Mleko\Narrator\Bundle\DependencyInjection\Compiler\ListenerPass */
    private $compilerPass;

    /** @var \Symfony\Component\DependencyInjection\Definition */
    private $defaultEmitterDefinition;

    public function setUp()
    {
        $this->container = $this->getMockBuilder(\Symfony\Component\DependencyInjection\ContainerBuilder::class)->getMock();

        $this->defaultEmitterDefinition = new \Symfony\Component\DependencyInjection\Definition(null, [null, []]);
        $this->container->method('getDefinition')->with('narrator.event_bus.default')->willReturn($this->defaultEmitterDefinition);

        $this->compilerPass = new \Mleko\Narrator\Bundle\DependencyInjection\Compiler\ListenerPass();
    }

    public function testProcess()
    {
        $this->container->expects($this->once())->method('findTaggedServiceIds')
            ->willReturn([
                'service1' => [
                    [
                        'event' => 'stdClass'
                    ]
                ]
            ]);

        $this->compilerPass->process($this->container);

        $listeners = $this->defaultEmitterDefinition->getArgument(1);
        $this->assertCount(1, $listeners);
    }

    public function testFailOnMissingEventName()
    {
        $this->container->expects($this->once())->method('findTaggedServiceIds')
            ->willReturn([
                'service1' => [
                    []
                ]
            ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('The narrator.listener must have event attribute');

        $this->compilerPass->process($this->container);
    }
}
