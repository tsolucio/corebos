<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Cache\Storage;

use Laminas\Cache\Exception;

interface PluginAwareInterface extends PluginCapableInterface
{
    /**
     * Register a plugin
     *
     * @param  Plugin\PluginInterface $plugin
     * @param  int $priority
     * @return StorageInterface
     * @throws Exception\LogicException
     */
    public function addPlugin(Plugin\PluginInterface $plugin, $priority = 1);

    /**
     * Unregister an already registered plugin
     *
     * @param  Plugin\PluginInterface $plugin
     * @return StorageInterface
     * @throws Exception\LogicException
     */
    public function removePlugin(Plugin\PluginInterface $plugin);
}
