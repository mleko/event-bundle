<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\Tests\Integration;


use Mleko\Narrator\EventBus;
use Mleko\Narrator\EventEmitter;
use Mleko\Narrator\Listener\EventTrap;
use org\bovigo\vfs\vfsStream;

/**
 * @runTestsInSeparateProcesses
 */
class SimpleEventListenerTest extends IntegrationTest
{
    public function testDefaultEventBus()
    {
        $config  = vfsStream::newFile("config.yml");
        $config->setContent(<<<'EOF'
imports:
    - { resource: services.xml }
narrator:
  event_bus:
    default:
      public: true
EOF
);
        $servicesConfig = vfsStream::newFile("services.xml");
        $servicesConfig->setContent(<<<'EOF'
<?xml version="1.0" encoding="UTF-8" ?>
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service class="Mleko\Narrator\Bundle\Tests\Integration\TestApp\Counter" id="narrator.listener.counter" public="true">
            <tag name="narrator.listener" event="stdClass"/>
        </service>
    </services>
</container>
EOF
        );
        $this->root->addChild($config);
        $this->root->addChild($servicesConfig);
        $this->kernel->setConfigPath($config->url());
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
      public: true
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

    public function testServiceResolver()
    {
        $servicesConfig = vfsStream::newFile("config.yml");
        $servicesConfig->setContent(<<<'EOF'
services:
    custom_resolver:
        class: Mleko\Narrator\ListenerResolver\InstanceOfResolver
    private_service:
        class: stdClass
        public: false
narrator:
  event_bus:
    default:
        resolver:
            type: service
            service_id: custom_resolver
        public: true
EOF
        );
        $this->root->addChild($servicesConfig);
        $this->kernel->setConfigPath($servicesConfig->url());
        $this->kernel->boot();

        $container = $this->kernel->getContainer();
        $this->assertTrue($container->has("narrator.event_bus.default"));
        /** @var EventBus $bus */
        $bus = $container->get("narrator.event_bus.default");
        $bus->subscribe(\Exception::class, $trap = new EventTrap());
        $bus->emit($e = new \RuntimeException());
        $this->assertContains($e, $trap->getTrappedEvents());
        $this->assertFalse($container->has("private_service"));
    }

    public function testPrivateBus()
    {
        $servicesConfig = vfsStream::newFile("config.yml");
        $servicesConfig->setContent(<<<'EOF'
services:
    test:
        alias: narrator.event_bus.default
        public: true
narrator:
  event_bus:
    default:
        resolver:
            type: instanceof
        public: false
EOF
        );
        $this->root->addChild($servicesConfig);
        $this->kernel->setConfigPath($servicesConfig->url());
        $this->kernel->boot();

        $container = $this->kernel->getContainer();
        $this->assertFalse($container->has("narrator.event_bus.default"), "Bus should not be visible directly");
        $this->assertTrue($container->has("test"), "Bus should available via alias");
        $bus = $container->get("test");
        $this->assertTrue($bus instanceof EventBus);
    }
}
