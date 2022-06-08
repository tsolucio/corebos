<?php

namespace Laminas\Cache\Storage\Adapter;

use ArrayObject;
use Laminas\Cache\Exception;
use Laminas\Cache\Storage\Capabilities;
use Laminas\Cache\Storage\FlushableInterface;
use MongoDB\BSON\UTCDateTime as MongoDate;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Driver\Exception\Exception as MongoDriverException;
use stdClass;
use Traversable;

use function array_key_exists;
use function assert;
use function class_exists;
use function extension_loaded;
use function get_class;
use function gettype;
use function is_array;
use function is_object;
use function microtime;
use function round;
use function sprintf;

/**
 * Cache storage adapter for ext-mongodb
 *
 * If you are using ext-mongo, use the MongoDb adapter instead.
 */
class ExtMongoDb extends AbstractAdapter implements FlushableInterface
{
    /**
     * Has this instance be initialized
     *
     * @var bool
     */
    private $initialized = false;

    /**
     * the mongodb resource manager
     *
     * @var null|ExtMongoDbResourceManager
     */
    private $resourceManager;

    /**
     * The mongodb resource id
     *
     * @var null|string
     */
    private $resourceId;

    /**
     * The namespace prefix
     *
     * @var string
     */
    private $namespacePrefix = '';

    /**
     * @param  null|array|Traversable|AdapterOptions $options
     * @throws Exception\ExtensionNotLoadedException
     */
    public function __construct($options = null)
    {
        if (! extension_loaded('mongodb') || ! class_exists(Client::class)) {
            throw new Exception\ExtensionNotLoadedException(
                'mongodb extension not loaded or Mongo PHP client library not installed'
            );
        }

        parent::__construct($options);

        $initialized = &$this->initialized;

        $this->getEventManager()->attach(
            'option',
            function () use (&$initialized) {
                $initialized = false;
            }
        );
    }

    /**
     * get mongodb resource
     */
    private function getMongoCollection(): Collection
    {
        $this->initialize();
        $resourceId = $this->resourceId;
        assert($resourceId !== null);
        return $this->resourceManager->getResource($resourceId);
    }

    /**
     * @param  array|Traversable|AdapterOptions|ExtMongoDbOptions $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (! $options instanceof ExtMongoDbOptions) {
            /** @psalm-suppress PossiblyInvalidArgument */
            $options = new ExtMongoDbOptions($options);
        }

        parent::setOptions($options);
        return $this;
    }

    /**
     * Get options.
     *
     * @see    setOptions()
     *
     * @return ExtMongoDbOptions
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        if (! $options instanceof ExtMongoDbOptions) {
            $options = new ExtMongoDbOptions($options->toArray());
            $this->setOptions($options);
        }

        return $options;
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalGetItem(&$normalizedKey, &$success = null, &$casToken = null)
    {
        $result  = $this->fetchFromCollection($normalizedKey);
        $success = false;

        if (null === $result) {
            return null;
        }

        self::ensureArrayType($result);

        if (isset($result['expires'])) {
            if (! $result['expires'] instanceof MongoDate) {
                throw new Exception\RuntimeException(sprintf(
                    "The found item _id '%s' for key '%s' is not a valid cache item"
                    . ": the field 'expired' isn't an instance of MongoDate, '%s' found instead",
                    (string) $result['_id'],
                    $this->namespacePrefix . $normalizedKey,
                    is_object($result['expires']) ? get_class($result['expires']) : gettype($result['expires'])
                ));
            }

            if ($result['expires']->toDateTime() < (new MongoDate())->toDateTime()) {
                $this->internalRemoveItem($normalizedKey);
                return;
            }
        }

        if (! array_key_exists('value', $result)) {
            throw new Exception\RuntimeException(sprintf(
                "The found item _id '%s' for key '%s' is not a valid cache item: missing the field 'value'",
                (string) $result['_id'],
                $this->namespacePrefix . $normalizedKey
            ));
        }

        $success = true;

        return $casToken = $result['value'];
    }

    /**
     * @param mixed $result
     */
    private static function ensureArrayType(&$result): void
    {
        if ($result instanceof ArrayObject) {
            $result = $result->getArrayCopy();
        }

        if (! is_array($result)) {
            return;
        }

        foreach ($result as &$value) {
            self::ensureArrayType($value);
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalSetItem(&$normalizedKey, &$value)
    {
        $mongo     = $this->getMongoCollection();
        $key       = $this->namespacePrefix . $normalizedKey;
        $ttl       = $this->getOptions()->getTTl();
        $cacheItem = [
            'key'   => $key,
            'value' => $value,
        ];

        if ($ttl > 0) {
            $ttlSeconds           = round((microtime(true) + $ttl) * 1000);
            $cacheItem['expires'] = new MongoDate($ttlSeconds);
        }

        try {
            $mongo->deleteOne(['key' => $key]);
            $result = $mongo->insertOne($cacheItem);
        } catch (MongoDriverException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return null !== $result && $result->isAcknowledged();
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\RuntimeException
     */
    protected function internalRemoveItem(&$normalizedKey)
    {
        try {
            $result = $this->getMongoCollection()->deleteOne(['key' => $this->namespacePrefix . $normalizedKey]);
        } catch (MongoDriverException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        return null !== $result && $result->getDeletedCount() > 0;
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $result = (object) $this->getMongoCollection()->drop();
        return ((float) 1) === $result->ok;
    }

    /**
     * {@inheritDoc}
     */
    protected function internalGetCapabilities()
    {
        if ($this->capabilities) {
            return $this->capabilities;
        }

        return $this->capabilities  = new Capabilities(
            $this,
            $this->capabilityMarker = new stdClass(),
            [
                'supportedDatatypes' => [
                    'NULL'     => true,
                    'boolean'  => true,
                    'integer'  => true,
                    'double'   => true,
                    'string'   => true,
                    'array'    => true,
                    'object'   => false,
                    'resource' => false,
                ],
                'supportedMetadata'  => [
                    '_id',
                ],
                'minTtl'             => 1,
                'staticTtl'          => true,
                'maxKeyLength'       => 255,
                'namespaceIsPrefix'  => true,
            ]
        );
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception\ExceptionInterface
     */
    protected function internalGetMetadata(&$normalizedKey)
    {
        $result = $this->fetchFromCollection($normalizedKey);
        return null !== $result ? ['_id' => $result['_id']] : false;
    }

    /**
     * Return raw records from MongoCollection
     *
     * @return array|null|object
     * @throws Exception\RuntimeException
     */
    private function fetchFromCollection(string $normalizedKey)
    {
        try {
            return $this->getMongoCollection()->findOne(['key' => $this->namespacePrefix . $normalizedKey]);
        } catch (MongoDriverException $e) {
            throw new Exception\RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $options = $this->getOptions();

        $this->resourceManager = $options->getResourceManager();
        $this->resourceId      = $options->getResourceId();
        $namespace             = $options->getNamespace();
        $this->namespacePrefix = $namespace === '' ? '' : $namespace . $options->getNamespaceSeparator();
        $this->initialized     = true;
    }
}
