<?php

namespace Laminas\Cache\Storage\Adapter;

/**
 * Options for the ext-mongodb adapter implementation.
 *
 * If you are using ext-mongo, use the MongoDbOptions class instead.
 */
class ExtMongoDbOptions extends AdapterOptions
{
    // @codingStandardsIgnoreStart
    /**
     * Prioritized properties ordered by prio to be set first
     * in case a bulk of options sets set at once
     *
     * @var string[]
     */
    protected $__prioritizedProperties__ = [
        'resource_manager',
        'resource_id'
    ];
    // @codingStandardsIgnoreEnd

    /**
     * The namespace separator
     *
     * @var string
     */
    private $namespaceSeparator = ':';

    /**
     * The ext-mongodb resource manager
     *
     * @var null|ExtMongoDbResourceManager
     */
    private $resourceManager;

    /**
     * The resource id of the resource manager
     *
     * @var string
     */
    private $resourceId = 'default';

    /**
     * Set namespace separator
     *
     * @param  string $namespaceSeparator
     * @return self Provides a fluent interface
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
     * Set the ext-mongodb resource manager to use
     *
     * @return self Provides a fluent interface
     */
    public function setResourceManager(?ExtMongoDbResourceManager $resourceManager = null)
    {
        if ($this->resourceManager !== $resourceManager) {
            $this->triggerOptionEvent('resource_manager', $resourceManager);

            $this->resourceManager = $resourceManager;
        }

        return $this;
    }

    /**
     * Get the ext-mongodb resource manager
     *
     * @return ExtMongoDbResourceManager
     */
    public function getResourceManager()
    {
        return $this->resourceManager ?: $this->resourceManager = new ExtMongoDbResourceManager();
    }

    /**
     * Get the ext-mongodb resource id
     *
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * Set the ext-mongodb resource id
     *
     * @param string $resourceId
     * @return $this Provides a fluent interface
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
     * Set the ext-mongodb server
     *
     * @param string $server
     * @return $this Provides a fluent interface
     */
    public function setServer($server)
    {
        $this->getResourceManager()->setServer($this->getResourceId(), $server);
        return $this;
    }

    /**
     * @param array $connectionOptions
     * @return $this Provides a fluent interface
     */
    public function setConnectionOptions(array $connectionOptions)
    {
        $this->getResourceManager()->setConnectionOptions($this->getResourceId(), $connectionOptions);
        return $this;
    }

    /**
     * @param array $driverOptions ext-mongodb driver options
     * @return $this Provides a fluent interface
     */
    public function setDriverOptions(array $driverOptions)
    {
        $this->getResourceManager()->setDriverOptions($this->getResourceId(), $driverOptions);
        return $this;
    }

    /**
     * @param string $database
     * @return $this Provides a fluent interface
     */
    public function setDatabase($database)
    {
        $this->getResourceManager()->setDatabase($this->getResourceId(), $database);
        return $this;
    }

    /**
     * @param string $collection
     * @return $this Provides a fluent interface
     */
    public function setCollection($collection)
    {
        $this->getResourceManager()->setCollection($this->getResourceId(), $collection);
        return $this;
    }
}
