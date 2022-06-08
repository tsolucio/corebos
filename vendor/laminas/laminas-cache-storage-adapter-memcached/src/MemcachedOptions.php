<?php

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception;
use Memcached as MemcachedResource;

use function sprintf;
use function strlen;
use function trigger_error;

use const E_USER_DEPRECATED;

/**
 * These are options specific to the Memcached adapter
 */
class MemcachedOptions extends AdapterOptions
{
    // @codingStandardsIgnoreStart
    /**
     * Prioritized properties ordered by prio to be set first
     * in case a bulk of options sets set at once
     *
     * @var string[]
     */
    protected $__prioritizedProperties__ = ['resource_manager', 'resource_id'];
    // @codingStandardsIgnoreEnd

    /**
     * The namespace separator
     *
     * @var string
     */
    protected $namespaceSeparator = ':';

    /**
     * The memcached resource manager
     *
     * @var null|MemcachedResourceManager
     */
    protected $resourceManager;

    /**
     * The resource id of the resource manager
     *
     * @var string
     */
    protected $resourceId = 'default';

    /**
     * Set namespace.
     *
     * The option Memcached::OPT_PREFIX_KEY will be used as the namespace.
     * It can't be longer than 128 characters.
     *
     * @see AdapterOptions::setNamespace()
     * @see MemcachedOptions::setPrefixKey()
     *
     * @param string $namespace
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setNamespace($namespace)
    {
        $namespace = (string) $namespace;

        if (128 < strlen($namespace)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a prefix key of no longer than 128 characters',
                __METHOD__
            ));
        }

        parent::setNamespace($namespace);
        return $this;
    }

    /**
     * Set namespace separator
     *
     * @param  string $namespaceSeparator
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setNamespaceSeparator($namespaceSeparator)
    {
        $namespaceSeparator = (string) $namespaceSeparator;
        if ($this->namespaceSeparator !== $namespaceSeparator) {
            $this->triggerOptionEvent('namespace_separator', $namespaceSeparator);
            $this->namespaceSeparator = $namespaceSeparator;
        }
        return $this;
    }

    /**
     * Get namespace separator
     *
     * @return string
     */
    public function getNamespaceSeparator()
    {
        return $this->namespaceSeparator;
    }

    /**
     * A memcached resource to share
     *
     * @deprecated Please use the resource manager instead
     *
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setMemcachedResource(?MemcachedResource $memcachedResource = null)
    {
        trigger_error(
            'This method is deprecated and will be removed in the feature'
            . ', please use the resource manager instead',
            E_USER_DEPRECATED
        );

        if ($memcachedResource !== null) {
            $this->triggerOptionEvent('memcached_resource', $memcachedResource);
            $resourceManager = $this->getResourceManager();
            $resourceId      = $this->getResourceId();
            $resourceManager->setResource($resourceId, $memcachedResource);
        }
        return $this;
    }

    /**
     * Get memcached resource to share
     *
     * @deprecated Please use the resource manager instead
     *
     * @return MemcachedResource
     */
    public function getMemcachedResource()
    {
        trigger_error(
            'This method is deprecated and will be removed in the feature'
            . ', please use the resource manager instead',
            E_USER_DEPRECATED
        );

        return $this->resourceManager->getResource($this->getResourceId());
    }

    /**
     * Set the memcached resource manager to use
     *
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setResourceManager(?MemcachedResourceManager $resourceManager = null)
    {
        if ($this->resourceManager !== $resourceManager) {
            $this->triggerOptionEvent('resource_manager', $resourceManager);
            $this->resourceManager = $resourceManager;
        }
        return $this;
    }

    /**
     * Get the memcached resource manager
     *
     * @return MemcachedResourceManager
     */
    public function getResourceManager()
    {
        if (! $this->resourceManager) {
            $this->resourceManager = new MemcachedResourceManager();
        }
        return $this->resourceManager;
    }

    /**
     * Get the memcached resource id
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set the memcached resource id
     *
     * @param string $resourceId
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setResourceId($resourceId)
    {
        $resourceId = (string) $resourceId;
        if ($this->resourceId !== $resourceId) {
            $this->triggerOptionEvent('resource_id', $resourceId);
            $this->resourceId = $resourceId;
        }
        return $this;
    }

    /**
     * Get the persistent id
     *
     * @return string
     */
    public function getPersistentId()
    {
        return $this->getResourceManager()->getPersistentId($this->getResourceId());
    }

    /**
     * Set the persistent id
     *
     * @param string $persistentId
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setPersistentId($persistentId)
    {
        $this->triggerOptionEvent('persistent_id', $persistentId);
        $this->getResourceManager()->setPersistentId($this->getResourceId(), $persistentId);
        return $this;
    }

    /**
     * Add a server to the list
     *
     * @deprecated Please use the resource manager instead
     *
     * @param string $host
     * @param int $port
     * @param int $weight
     * @return MemcachedOptions Provides a fluent interface
     */
    public function addServer($host, $port = 11211, $weight = 0)
    {
        trigger_error(
            'This method is deprecated and will be removed in the feature'
            . ', please use the resource manager instead',
            E_USER_DEPRECATED
        );

        $this->getResourceManager()->addServer($this->getResourceId(), [
            'host'   => $host,
            'port'   => $port,
            'weight' => $weight,
        ]);

        return $this;
    }

    /**
     * Set a list of memcached servers to add on initialize
     *
     * @param string|array $servers list of servers
     * @return MemcachedOptions Provides a fluent interface
     * @throws Exception\InvalidArgumentException
     */
    public function setServers($servers)
    {
        $this->getResourceManager()->setServers($this->getResourceId(), $servers);
        return $this;
    }

    /**
     * Get Servers
     *
     * @return array
     */
    public function getServers()
    {
        return $this->getResourceManager()->getServers($this->getResourceId());
    }

    /**
     * Set libmemcached options
     *
     * @link http://php.net/manual/memcached.constants.php
     *
     * @param array $libOptions
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setLibOptions(array $libOptions)
    {
        $this->getResourceManager()->setLibOptions($this->getResourceId(), $libOptions);
        return $this;
    }

    /**
     * Set libmemcached option
     *
     * @deprecated Please use lib_options or the resource manager instead
     *
     * @link http://php.net/manual/memcached.constants.php
     *
     * @param string|int $key
     * @param mixed $value
     * @return MemcachedOptions Provides a fluent interface
     */
    public function setLibOption($key, $value)
    {
        trigger_error(
            'This method is deprecated and will be removed in the feature'
            . ', please use "lib_options" or the resource manager instead',
            E_USER_DEPRECATED
        );

        $this->getResourceManager()->setLibOption($this->getResourceId(), $key, $value);
        return $this;
    }

    /**
     * Get libmemcached options
     *
     * @link http://php.net/manual/memcached.constants.php
     *
     * @return array
     */
    public function getLibOptions()
    {
        return $this->getResourceManager()->getLibOptions($this->getResourceId());
    }

    /**
     * Get libmemcached option
     *
     * @deprecated Please use lib_options or the resource manager instead
     *
     * @link http://php.net/manual/memcached.constants.php
     *
     * @param string|int $key
     * @return mixed
     */
    public function getLibOption($key)
    {
        trigger_error(
            'This method is deprecated and will be removed in the feature'
            . ', please use "lib_options" or the resource manager instead',
            E_USER_DEPRECATED
        );

        return $this->getResourceManager()->getLibOption($this->getResourceId(), $key);
    }
}
