<?php


namespace Mleko\Narrator\Bundle\Tests\Integration;


use Mleko\Narrator\EventEmitter;
use org\bovigo\vfs\vfsStream;

/**
 * @runTestsInSeparateProcesses
 */
class SimpleEventListenerTest extends IntegrationTest
{
    public function testDefaultEventBus()
    {
        $servicesConfig = vfsStream::newFile("services.xml");
        $servicesConfig->setContent(<<<'EOF'
<?xml version="1.0" encoding="UTF-8" ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service class="Mleko\Narrator\Bundle\Tests\Integration\TestApp\Counter" id="narrator.listener.counter">
            <tag name="narrator.listener" event="stdClass"/>
        </service>
    </services>
</container>
EOF
        );
        $this->root->addChild($servicesConfig);
        $this->kernel->setConfigPath($servicesConfig->url());
        $this->kernel->boot();

        $container = $this->kernel->getContainer();
        $this->assertTrue($container->has("narrator.event_bus.default"));
        $this->assertTrue($container->has("narrator.listener.counter"));
        $container->get("narrator.event_bus.default")->emit(new \stdClass());
        $this->assertEquals(1, $container->get("narrator.listener.counter")->getCount());
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @expectedExceptionMessageRegExp "You have requested a non-existent service \"narrator.event_bus.invalid\"\."
     */
    public function testMissingEventBus()
    {
        $this->kernel->boot();

        $container = $this->kernel->getContainer();
        $container->get("narrator.event_bus.invalid");
    }

    public function testCustomEventBus()
    {
        $servicesConfig = vfsStream::newFile("config.yml");
        $servicesConfig->setContent(<<<'EOF'
narrator:
  event_bus:
    default: ~
    inheritance:
      resolver:
        type: instanceof
EOF
        );
        $this->root->addChild($servicesConfig);
        $this->kernel->setConfigPath($servicesConfig->url());
        $this->kernel->boot();

        $container = $this->kernel->getContainer();
        $this->assertTrue($container->has("narrator.event_bus.inheritance"));
        /** @var EventEmitter $eventEmitter */
        $this->assertNotNull($container->get("narrator.event_bus.inheritance"));
    }
}
