<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Cache\Service;

use Interop\Container\ContainerInterface;
use Laminas\Cache\Storage\AdapterPluginManager;
use Laminas\Cache\Storage\PluginManager;
use Laminas\Cache\StorageFactory;

trait PluginManagerLookupTrait
{
    /**
     * Prepare the storage factory with the adapter and plugins plugin managers.
     *
     * @param ContainerInterface $container
     * @return void
     */
    private function prepareStorageFactory(ContainerInterface $container)
    {
        StorageFactory::setAdapterPluginManager($this->lookupStorageAdapterPluginManager($container));
        StorageFactory::setPluginManager($this->lookupStoragePluginManager($container));
    }

    /**
     * Lookup the storage adapter plugin manager.
     *
     * Returns the Laminas\Cache\Storage\AdapterPluginManager service if present,
     * or creates a new instance otherwise.
     *
     * @param ContainerInterface $container
     * @return AdapterPluginManager
     */
    private function lookupStorageAdapterPluginManager(ContainerInterface $container)
    {
        if ($container->has(AdapterPluginManager::class)) {
            return $container->get(AdapterPluginManager::class);
        }
        return new AdapterPluginManager($container);
    }

    /**
     * Lookup the storage plugins plugin manager.
     *
     * Returns the Laminas\Cache\Storage\PluginManager service if present, or
     * creates a new instance otherwise.
     *
     * @param ContainerInterface $container
     * @return PluginManager
     */
    private function lookupStoragePluginManager(ContainerInterface $container)
    {
        if ($container->has(PluginManager::class)) {
            return $container->get(PluginManager::class);
        }
        return new PluginManager($container);
    }
}
