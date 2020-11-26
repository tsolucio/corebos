<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Cache\Psr\SimpleCache;

use Psr\SimpleCache\CacheException as PsrCacheException;
use RuntimeException;

class SimpleCacheException extends RuntimeException implements PsrCacheException
{
}
