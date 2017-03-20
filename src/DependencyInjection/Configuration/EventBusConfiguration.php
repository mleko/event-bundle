<?php


namespace Mleko\Narrator\Bundle\DependencyInjection\Configuration;


use Mleko\Narrator\BasicEventBus;
use Mleko\Narrator\ListenerResolver\InstanceOfResolver;
use Mleko\Narrator\ListenerResolver\NameBasedResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class EventBusConfiguration
{
    /**
     * @param $buses
     * @param ContainerBuilder $container
     */
    public function configureEventBus($buses, ContainerBuilder $container)
    {
        foreach ($buses as $busName => $busConfig) {
            $resolverServiceId = "narrator.event_bus.$busName.resolver";
            $resolverConfig = isset($busConfig['resolver']) ? $busConfig['resolver'] : [];
            $container->setDefinition(
                $resolverServiceId,
                $this->buildResolver($resolverConfig)
            );

            $container->setDefinition(
                "narrator.event_bus.$busName",
                new Definition(
                    BasicEventBus::class,
                    [
                        new Reference($resolverServiceId),
                        []
                    ]
                )
            );
        }
    }

    private function buildResolver($resolverConfig)
    {
        $resolverType = isset($resolverConfig['type']) ? $resolverConfig['type'] : 'name';
        switch ($resolverType) {
            case 'instanceof':
                return new Definition(InstanceOfResolver::class);
            case 'service':
                return new Reference($resolverType['service_id']);
            default:
                $nameExtractorId = isset($resolverConfig['name_extractor']) ? $resolverConfig['name_extractor'] : "narrator.name_extractor.class_name";
                return new Definition(
                    NameBasedResolver::class,
                    [new Reference($nameExtractorId)]
                );
        }
    }
}
