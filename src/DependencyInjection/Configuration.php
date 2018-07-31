<?php
/**
 * Narrator Bundle
 *
 * @link      http://github.com/mleko/narrator-bundle
 * @copyright Copyright (c) 2017 Daniel Król
 * @license   MIT
 */


namespace Mleko\Narrator\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('narrator');

        $rootNode
            ->children()
                ->arrayNode('event_bus')
                ->defaultValue(['default' => ['resolver' => ['type' => 'name', "name_extractor" => "narrator.name_extractor.class_name"], 'public' => false]])
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype("array")
                ->children()
                    ->booleanNode('public')
                        ->defaultValue(false)
                    ->end()
                    ->arrayNode('resolver')
                        ->children()
                            ->enumNode('type')
                            ->values(['name', 'instanceof', 'service'])
                            ->isRequired()
                        ->end()
                            ->scalarNode('name_extractor')
                            ->defaultValue("narrator.name_extractor.class_name")
                        ->end()
                            ->scalarNode('service_id')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
