<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

use Laminas\Cache\PatternPluginManager;
use Laminas\ServiceManager\ServiceManager;

call_user_func(function () {
    $target = method_exists(ServiceManager::class, 'configure')
        ? PatternPluginManager\PatternPluginManagerV3Polyfill::class
        : PatternPluginManager\PatternPluginManagerV2Polyfill::class;

    class_alias($target, PatternPluginManager::class);
});
