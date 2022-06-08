<?php

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Exception\Exception as MongoDriverException;

use function assert;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function is_string;
use function sprintf;

/**
 * Resource manager for the ext-mongodb adapter.
 *
 * If you are using ext-mongo, use the MongoDbResourceManager instead.
 */
class ExtMongoDbResourceManager
{
    /**
     * Registered resources
     *
     * @var array[]
     */
    private $resources = [];

    /**
     * Check if a resource exists
     *
     * @param string $id
     * @return bool
     */
    public function hasResource($id)
    {
        return isset($this->resources[$id]);
    }

    /**
     * Set a resource
     *
     * @param string $id
     * @param array|Collection $resource
     * @return self Provides a fluent interface
     * @throws Exception\RuntimeException
     */
    public function setResource($id, $resource)
    {
        if ($resource instanceof Collection) {
            $this->resources[$id] = [
                'db'                  => $resource->getDatabaseName(),
                'collection'          => (string) $resource,
                'collection_instance' => $resource,
            ];
            return $this;
        }

        if (! is_array($resource)) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects an array or %s; received %s',
                __METHOD__,
                Collection::class,
                is_object($resource) ? get_class($resource) : gettype($resource)
            ));
        }

        $this->resources[$id] = $resource;
        return $this;
    }

    /**
     * Instantiate and return the Collection resource
     *
     * @param string $id
     * @return Collection
     * @throws Exception\RuntimeException
     */
    public function getResource($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $resource = $this->resources[$id];
        if (! isset($resource['collection_instance'])) {
            try {
                /** @psalm-suppress MixedAssignment */
                $clientInstance = $resource['client_instance'] ?? null;
                if (! $clientInstance instanceof Client) {
                    $clientInstance = new Client(
                        (string) ($resource['server'] ?? ''),
                        (array) ($resource['connection_options'] ?? []),
                        (array) ($resource['driver_options'] ?? [])
                    );
                }

                $resource['client_instance'] = $clientInstance;

                $collection = $clientInstance->selectCollection(
                    (string) ($resource['db'] ?? 'laminas'),
                    (string) ($resource['collection'] ?? 'cache')
                );
                $collection->createIndex(['key' => 1]);

                $this->resources[$id]['collection_instance'] = $collection;
            } catch (MongoDriverException $e) {
                throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $instance = $this->resources[$id]['collection_instance'];
        assert($instance instanceof Collection);

        return $instance;
    }

    /**
     * @param string $id
     * @param string $server
     * @return void
     */
    public function setServer($id, $server)
    {
        $this->resources[$id]['server'] = (string) $server;

        unset($this->resources[$id]['client_instance']);
        unset($this->resources[$id]['collection_instance']);
    }

    /**
     * @param string $id
     * @return null|string
     * @throws Exception\RuntimeException If no matching resource discovered.
     */
    public function getServer($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $server = $this->resources[$id]['server'] ?? null;
        if (! is_string($server)) {
            return null;
        }

        return $server;
    }

    /**
     * @param string $id
     * @param array $connectionOptions
     * @return void
     */
    public function setConnectionOptions($id, array $connectionOptions)
    {
        $this->resources[$id]['connection_options'] = $connectionOptions;

        unset($this->resources[$id]['client_instance']);
        unset($this->resources[$id]['collection_instance']);
    }

    /**
     * @param string $id
     * @return array
     * @throws Exception\RuntimeException If no matching resource discovered.
     */
    public function getConnectionOptions($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $options = $this->resources[$id]['connection_options'] ?? [];
        if (! is_array($options)) {
            return [];
        }

        return $options;
    }

    /**
     * @param string $id
     * @param array $driverOptions
     * @return void
     */
    public function setDriverOptions($id, array $driverOptions)
    {
        $this->resources[$id]['driver_options'] = $driverOptions;

        unset($this->resources[$id]['client_instance']);
        unset($this->resources[$id]['collection_instance']);
    }

    /**
     * @param string $id
     * @return array
     * @throws Exception\RuntimeException If no matching resource discovered.
     */
    public function getDriverOptions($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $options = $this->resources[$id]['driver_options'] ?? [];
        if (! is_array($options)) {
            return [];
        }

        return $options;
    }

    /**
     * @param string $id
     * @param string $database
     * @return void
     */
    public function setDatabase($id, $database)
    {
        $this->resources[$id]['db'] = (string) $database;

        unset($this->resources[$id]['collection_instance']);
    }

    /**
     * @param string $id
     * @return string
     * @throws Exception\RuntimeException If no matching resource discovered.
     */
    public function getDatabase($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $db = $this->resources[$id]['db'] ?? '';
        if (! is_string($db)) {
            return '';
        }

        return $db;
    }

    /**
     * @param string $id
     * @param string $collection
     * @return void
     */
    public function setCollection($id, $collection)
    {
        $this->resources[$id]['collection'] = (string) $collection;

        unset($this->resources[$id]['collection_instance']);
    }

    /**
     * @param string $id
     * @return string
     * @throws Exception\RuntimeException If no matching resource discovered.
     */
    public function getCollection($id)
    {
        if (! $this->hasResource($id)) {
            throw new Exception\RuntimeException("No resource with id '{$id}'");
        }

        $collection = $this->resources[$id]['collection'] ?? '';
        if (! is_string($collection)) {
            return '';
        }

        return $collection;
    }
}
