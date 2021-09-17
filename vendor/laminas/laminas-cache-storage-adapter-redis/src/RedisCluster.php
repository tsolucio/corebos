<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception;
use Laminas\Cache\Storage\Adapter\Exception\MetadataErrorException;
use Laminas\Cache\Storage\Adapter\Exception\RedisRuntimeException;
use Laminas\Cache\Storage\Capabilities;
use Laminas\Cache\Storage\ClearByNamespaceInterface;
use Laminas\Cache\Storage\ClearByPrefixInterface;
use Laminas\Cache\Storage\FlushableInterface;
use Redis;
use RedisCluster as RedisClusterFromExtension;
use RedisClusterException;
use RedisException;
use stdClass;
use Traversable;

use function array_key_exists;
use function array_values;
use function count;
use function extension_loaded;
use function in_array;
use function sprintf;
use function version_compare;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class RedisCluster extends AbstractAdapter implements
    ClearByNamespaceInterface,
    ClearByPrefixInterface,
    FlushableInterface
{
    /** @var RedisClusterFromExtension|null */
    private $resource;

    /** @var string|null */
    private $namespacePrefix;

    /** @var RedisClusterResourceManagerInterface|null */
    private $resourceManager;

    /**
     * @param null|array|Traversable|RedisClusterOptions $options
     * @psalm-param array<string,mixed>|RedisClusterOptions|Traversable<string,mixed> $options
     */
    public function __construct($options = null)
    {
        if (! extension_loaded('redis')) {
            throw new Exception\ExtensionNotLoadedException("Redis extension is not loaded");
        }

        /** @psalm-suppress PossiblyInvalidArgument */
        parent::__construct($options);
        $eventManager = $this->getEventManager();

        $eventManager->attach('option', function (): void {
            $this->resource         = null;
            $this->capabilities     = null;
            $this->capabilityMarker = null;
            $this->namespacePrefix  = null;
        });
    }

    /**
     * @param  array|Traversable|AdapterOptions $options
     * @return self
     */
    public function setOptions($options)
    {
        if (! $options instanceof RedisClusterOptions) {
            /** @psalm-suppress PossiblyInvalidArgument */
            $options = new RedisClusterOptions($options);
        }

        parent::setOptions($options);
        return $this;
    }

    /**
     * In RedisCluster, it is totally okay if just one primary server is being flushed.
     * If one or more primaries are not reachable, they will re-sync if they're coming back online.
     *
     * One has to connect to the primaries directly using {@see Redis::connect}.
     */
    public function flush(): bool
    {
        $resource                     = $this->getRedisResource();
        $anyMasterSuccessfullyFlushed = false;
        /** @psalm-var array<array-key,array{0:string,1:int}> $masters */
        $masters = $resource->_masters();

        foreach ($masters as [$host, $port]) {
            $redis = new Redis();
            try {
                $redis->connect($host, $port);
            } catch (RedisException $exception) {
                continue;
            }

            if (! $redis->flushAll()) {
                continue;
            }

            $anyMasterSuccessfullyFlushed = true;
        }

        return $anyMasterSuccessfullyFlushed;
    }

    private function getRedisResource(): RedisClusterFromExtension
    {
        if ($this->resource instanceof RedisClusterFromExtension) {
            return $this->resource;
        }

        $resourceManager = $this->getResourceManager();

        try {
            return $this->resource = $resourceManager->getResource();
        } catch (RedisClusterException $exception) {
            throw RedisRuntimeException::fromFailedConnection($exception);
        }
    }

    public function getOptions(): RedisClusterOptions
    {
        $options = parent::getOptions();
        if (! $options instanceof RedisClusterOptions) {
            $options       = new RedisClusterOptions($options);
            $this->options = $options;
        }

        return $options;
    }

    /**
     * @param string $namespace
     */
    public function clearByNamespace($namespace): bool
    {
        /** @psalm-suppress RedundantCast */
        $namespace = (string) $namespace;
        if ($namespace === '') {
            throw new Exception\InvalidArgumentException('Invalid namespace provided');
        }

        return $this->searchAndDelete('', $namespace);
    }

    /**
     * @param string $prefix
     */
    public function clearByPrefix($prefix): bool
    {
        /** @psalm-suppress RedundantCast */
        $prefix = (string) $prefix;
        if ($prefix === '') {
            throw new Exception\InvalidArgumentException('No prefix given');
        }

        $options = $this->getOptions();

        return $this->searchAndDelete($prefix, $options->getNamespace());
    }

    /**
     * @param string     $normalizedKey
     * @param bool|null  $success
     * @param mixed|null $casToken
     * @return mixed|null
     */
    protected function internalGetItem(&$normalizedKey, &$success = null, &$casToken = null)
    {
        $normalizedKeys = [$normalizedKey];
        $values         = $this->internalGetItems($normalizedKeys);
        if (! array_key_exists($normalizedKey, $values)) {
            $success = false;
            return null;
        }

        /** @psalm-suppress MixedAssignment */
        $value   = $casToken = $values[$normalizedKey];
        $success = true;
        return $value;
    }

    protected function internalGetItems(array &$normalizedKeys): array
    {
        /** @var array<int,string> $normalizedKeys */
        $normalizedKeys = array_values($normalizedKeys);
        $namespacedKeys = [];
        foreach ($normalizedKeys as $normalizedKey) {
            /** @psalm-suppress RedundantCast */
            $namespacedKeys[] = $this->createNamespacedKey((string) $normalizedKey);
        }

        $redis = $this->getRedisResource();

        try {
            /** @var array<int,mixed> $resultsByIndex */
            $resultsByIndex = $redis->mget($namespacedKeys);
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }

        $result = [];
        /** @psalm-suppress MixedAssignment */
        foreach ($resultsByIndex as $keyIndex => $value) {
            $normalizedKey = $normalizedKeys[$keyIndex];
            $namespacedKey = $namespacedKeys[$keyIndex];
            if ($value === false && ! $this->isFalseReturnValuePersisted($redis, $namespacedKey)) {
                continue;
            }

            /** @psalm-suppress MixedAssignment */
            $result[$normalizedKey] = $value;
        }

        return $result;
    }

    private function createNamespacedKey(string $key): string
    {
        if ($this->namespacePrefix !== null) {
            return $this->namespacePrefix . $key;
        }

        $options               = $this->getOptions();
        $namespace             = $options->getNamespace();
        $this->namespacePrefix = $namespace;
        if ($namespace !== '') {
            $this->namespacePrefix = $namespace . $options->getNamespaceSeparator();
        }

        return $this->namespacePrefix . $key;
    }

    /**
     * @param string $normalizedKey
     * @param mixed  $value
     */
    protected function internalSetItem(&$normalizedKey, &$value): bool
    {
        $redis   = $this->getRedisResource();
        $options = $this->getOptions();
        $ttl     = (int) $options->getTtl();

        $namespacedKey = $this->createNamespacedKey($normalizedKey);
        try {
            if ($ttl) {
                /**
                 * @psalm-suppress MixedArgument
                 * Redis & RedisCluster do allow mixed values when a serializer is configured.
                 */
                return $redis->setex($namespacedKey, $ttl, $value);
            }

            /**
             * @psalm-suppress MixedArgument
             * Redis & RedisCluster do allow mixed values when a serializer is configured.
             */
            return $redis->set($namespacedKey, $value);
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }
    }

    /**
     * @param string $normalizedKey
     */
    protected function internalRemoveItem(&$normalizedKey): bool
    {
        $redis = $this->getRedisResource();

        try {
            return $redis->del($this->createNamespacedKey($normalizedKey)) === 1;
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }
    }

    /**
     * @param string $normalizedKey
     */
    protected function internalHasItem(&$normalizedKey): bool
    {
        $redis = $this->getRedisResource();

        try {
            /** @psalm-var 0|1 $exists */
            $exists = $redis->exists($this->createNamespacedKey($normalizedKey));
            return (bool) $exists;
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }
    }

    protected function internalSetItems(array &$normalizedKeyValuePairs): array
    {
        $redis = $this->getRedisResource();
        $ttl   = (int) $this->getOptions()->getTtl();

        $namespacedKeyValuePairs = [];
        /** @psalm-suppress MixedAssignment */
        foreach ($normalizedKeyValuePairs as $normalizedKey => $value) {
            $namespacedKeyValuePairs[$this->createNamespacedKey((string) $normalizedKey)] = $value;
        }

        $successByKey = [];

        try {
            /** @psalm-suppress MixedAssignment */
            foreach ($namespacedKeyValuePairs as $key => $value) {
                if ($ttl) {
                    /**
                     * @psalm-suppress MixedArgument
                     * Redis & RedisCluster do allow mixed values when a serializer is configured.
                     */
                    $successByKey[$key] = $redis->setex($key, $ttl, $value);
                    continue;
                }

                /**
                 * @psalm-suppress MixedArgument
                 * Redis & RedisCluster do allow mixed values when a serializer is configured.
                 */
                $successByKey[$key] = $redis->set($key, $value);
            }
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }

        $statuses = [];
        foreach ($successByKey as $key => $success) {
            if ($success) {
                continue;
            }

            $statuses[] = $key;
        }

        return $statuses;
    }

    protected function internalGetCapabilities(): Capabilities
    {
        if ($this->capabilities !== null) {
            return $this->capabilities;
        }

        $this->capabilityMarker = new stdClass();
        $redisVersion           = $this->getRedisVersion();
        $serializer             = $this->hasSerializationSupport();
        $redisVersionLessThanV2 = version_compare($redisVersion, '2.0', '<');
        $redisVersionLessThanV3 = version_compare($redisVersion, '3.0', '<');
        $minTtl                 = $redisVersionLessThanV2 ? 0 : 1;
        $supportedMetadata      = ! $redisVersionLessThanV2 ? ['ttl'] : [];

        $this->capabilities = new Capabilities(
            $this,
            $this->capabilityMarker,
            [
                'supportedDatatypes' => $this->getSupportedDatatypes($serializer),
                'supportedMetadata'  => $supportedMetadata,
                'minTtl'             => $minTtl,
                'maxTtl'             => 0,
                'staticTtl'          => true,
                'ttlPrecision'       => 1,
                'useRequestTime'     => false,
                'maxKeyLength'       => $redisVersionLessThanV3 ? 255 : 512000000,
                'namespaceIsPrefix'  => true,
            ]
        );

        return $this->capabilities;
    }

    /**
     * @psalm-return array<string,mixed>
     */
    private function getSupportedDatatypes(bool $serializer): array
    {
        if ($serializer) {
            return [
                'NULL'     => true,
                'boolean'  => true,
                'integer'  => true,
                'double'   => true,
                'string'   => true,
                'array'    => 'array',
                'object'   => 'object',
                'resource' => false,
            ];
        }

        return [
            'NULL'     => 'string',
            'boolean'  => 'string',
            'integer'  => 'string',
            'double'   => 'string',
            'string'   => true,
            'array'    => false,
            'object'   => false,
            'resource' => false,
        ];
    }

    /**
     * @psalm-param RedisClusterOptions::OPT_* $option
     * @return mixed
     */
    private function getLibOption(int $option)
    {
        $resourceManager = $this->getResourceManager();
        return $resourceManager->getLibOption($option);
    }

    private function searchAndDelete(string $prefix, string $namespace): bool
    {
        $redis   = $this->getRedisResource();
        $options = $this->getOptions();

        $prefix = $namespace === '' ? '' : $namespace . $options->getNamespaceSeparator() . $prefix;

        /** @var array<array-key,string> $keys */
        $keys = $redis->keys($prefix . '*');
        if (! $keys) {
            return true;
        }

        return $redis->del($keys) === count($keys);
    }

    private function clusterException(
        RedisClusterException $exception,
        RedisClusterFromExtension $redis
    ): Exception\RuntimeException {
        return RedisRuntimeException::fromClusterException($exception, $redis);
    }

    /**
     * This method verifies that the return value from {@see RedisClusterFromExtension::get} or
     * {@see RedisClusterFromExtension::mget} is `false` because the key does not exist or because the keys value
     * is `false` at type-level.
     */
    private function isFalseReturnValuePersisted(RedisClusterFromExtension $redis, string $key): bool
    {
        /** @psalm-suppress MixedAssignment */
        $serializer = $this->getLibOption(RedisClusterFromExtension::OPT_SERIALIZER);
        if ($serializer === RedisClusterFromExtension::SERIALIZER_NONE) {
            return false;
        }

        try {
            /** @psalm-var 0|1 $exists */
            $exists = $redis->exists($key);
            return (bool) $exists;
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }
    }

    /**
     * Internal method to get metadata of an item.
     *
     * @param  string $normalizedKey
     * @return array|bool Metadata on success, false on failure
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadata(&$normalizedKey)
    {
        /** @psalm-suppress RedundantCastGivenDocblockType */
        $namespacedKey = $this->createNamespacedKey((string) $normalizedKey);
        $redis         = $this->getRedisResource();
        $metadata      = [];
        $capabilities  = $this->internalGetCapabilities();
        try {
            if (in_array('ttl', $capabilities->getSupportedMetadata(), true)) {
                $ttl             = $this->detectTtlForKey($redis, $namespacedKey);
                $metadata['ttl'] = $ttl;
            }
        } catch (MetadataErrorException $exception) {
            return false;
        } catch (RedisClusterException $exception) {
            throw $this->clusterException($exception, $redis);
        }

        return $metadata;
    }

    private function detectTtlForKey(RedisClusterFromExtension $redis, string $namespacedKey): ?int
    {
        $redisVersion = $this->getRedisVersion();
        $ttl          = $redis->ttl($namespacedKey);

        // redis >= 2.8
        // The command 'ttl' returns -2 if the item does not exist
        // and -1 if the item has no associated expire
        if (version_compare($redisVersion, '2.8', '>=')) {
            if ($ttl <= -2) {
                throw new MetadataErrorException();
            }

            return $ttl === -1 ? null : $ttl;
        }

        // redis >= 2.6, < 2.8
        // The command 'tttl' returns -1 if the item does not exist or the item has no associated expire
        if (version_compare($redisVersion, '2.6', '>=')) {
            if ($ttl <= -1) {
                if (! $this->internalHasItem($namespacedKey)) {
                    throw new MetadataErrorException();
                }

                return null;
            }

            return $ttl;
        }

        // redis >= 2, < 2.6
        // The command 'pttl' is not supported but 'ttl'
        // The command 'ttl' returns 0 if the item does not exist same as if the item is going to be expired
        // NOTE: In case of ttl=0 we return false because the item is going to be expired in a very near future
        //       and then doesn't exist any more
        if (version_compare($redisVersion, '2', '>=')) {
            if ($ttl <= -1) {
                if (! $this->internalHasItem($namespacedKey)) {
                    throw new MetadataErrorException();
                }

                return null;
            }

            return $ttl;
        }

        throw new Exception\LogicException(
            sprintf(
                '%s must not be called for current redis version.',
                __METHOD__
            )
        );
    }

    private function getRedisVersion(): string
    {
        $resourceManager = $this->getResourceManager();
        return $resourceManager->getVersion();
    }

    private function hasSerializationSupport(): bool
    {
        $resourceManager = $this->getResourceManager();
        return $resourceManager->hasSerializationSupport($this);
    }

    private function getResourceManager(): RedisClusterResourceManagerInterface
    {
        if ($this->resourceManager !== null) {
            return $this->resourceManager;
        }

        return $this->resourceManager = new RedisClusterResourceManager($this->getOptions());
    }

    /**
     * @internal This is only used for unit testing. There should be no need to use this method in upstream projects.
     */
    public function setResourceManager(RedisClusterResourceManagerInterface $resourceManager): void
    {
        $this->resourceManager = $resourceManager;
    }
}
