<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\DependencyInjection\Configuration;


use Mleko\Narrator\BasicEventBus;
use Mleko\Narrator\ListenerResolver\InstanceOfResolver;
use Mleko\Narrator\ListenerResolver\NameBasedResolver;
use Symfony\Component\DependencyInjection\Alias;
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
            $resolver = $this->buildResolver($resolverConfig);

            if ($resolver instanceof Definition) {
                $container->setDefinition($resolverServiceId, $resolver);
            } elseif ($resolver instanceof Alias) {
                $container->setAlias($resolverServiceId, $resolver);
            }
            $busDefinition = new Definition(
                BasicEventBus::class,
                [
                    new Reference($resolverServiceId),
                    []
                ]
            );
            $busDefinition->setPublic($busConfig['public']);
            $container->setDefinition("narrator.event_bus.$busName", $busDefinition);
        }
    }

    /**
     * @param array $resolverConfig
     * @return Alias|Definition
     */
    private function buildResolver($resolverConfig)
    {
        $resolverType = isset($resolverConfig['type']) ? $resolverConfig['type'] : 'name';
        switch ($resolverType) {
            case 'instanceof':
                return new Definition(InstanceOfResolver::class);
            case 'service':
                return new Alias($resolverConfig['service_id'], false);
            default:
                $nameExtractorId = isset($resolverConfig['name_extractor']) ? $resolverConfig['name_extractor'] : "narrator.name_extractor.class_name";
                return new Definition(
                    NameBasedResolver::class,
                    [new Reference($nameExtractorId)]
                );
        }
    }
}
