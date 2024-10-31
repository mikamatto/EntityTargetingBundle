<?php

namespace Mikamatto\EntityTargetingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('mikamatto_entity_targeting');

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enable_cache')->defaultTrue()->end()
                ->integerNode('cache_expiration')->defaultValue(3600)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}