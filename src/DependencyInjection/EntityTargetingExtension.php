<?php

namespace Mikamatto\EntityTargetingBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EntityTargetingExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Pass config parameters to the container
        $container->setParameter('mikamatto_entity_targeting.enable_cache', $config['enable_cache']);
        $container->setParameter('mikamatto_entity_targeting.cache_expiration', $config['cache_expiration']);

        // Load service definitions
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');
    }
}