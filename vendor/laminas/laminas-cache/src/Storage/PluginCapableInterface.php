<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Cache\Storage;

use Laminas\EventManager\EventsCapableInterface;
use SplObjectStorage;

interface PluginCapableInterface extends EventsCapableInterface
{
    /**
     * Check if a plugin is registered
     *
     * @param  Plugin\PluginInterface $plugin
     * @return bool
     */
    public function hasPlugin(Plugin\PluginInterface $plugin);

    /**
     * Return registry of plugins
     *
     * @return SplObjectStorage
     */
    public function getPluginRegistry();
}
