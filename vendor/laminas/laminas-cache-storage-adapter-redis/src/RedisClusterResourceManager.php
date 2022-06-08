<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception\ExtensionNotLoadedException;
use Laminas\Cache\Exception\RuntimeException;
use Laminas\Cache\Storage\Adapter\Exception\RedisRuntimeException;
use Laminas\Cache\Storage\Plugin\PluginInterface;
use Laminas\Cache\Storage\Plugin\Serializer;
use Laminas\Cache\Storage\PluginCapableInterface;
use RedisCluster as RedisClusterFromExtension;
use RedisClusterException;

use function array_key_exists;
use function assert;
use function extension_loaded;

/**
 * @psalm-type RedisClusterInfoType = array<string,mixed>&array{redis_version:string}
 */
final class RedisClusterResourceManager implements RedisClusterResourceManagerInterface
{
    /** @var RedisClusterOptions */
    private $options;

    /** @psalm-var array<positive-int,mixed> */
    private $libraryOptions = [];

    public function __construct(RedisClusterOptions $options)
    {
        $this->options = $options;
        if (! extension_loaded('redis')) {
            throw new ExtensionNotLoadedException('Redis extension is not loaded');
        }
    }

    public function getVersion(): string
    {
        $versionFromOptions = $this->options->getRedisVersion();
        if ($versionFromOptions) {
            return $versionFromOptions;
        }

        $resource = $this->getResource();
        try {
            $info = $this->info($resource);
        } catch (RedisClusterException $exception) {
            throw RedisRuntimeException::fromClusterException($exception, $resource);
        }

        $version = $info['redis_version'];
        assert($version !== '');
        $this->options->setRedisVersion($version);

        return $version;
    }

    public function getResource(): RedisClusterFromExtension
    {
        try {
            $resource = $this->createRedisResource($this->options);
        } catch (RedisClusterException $exception) {
            throw RedisRuntimeException::fromFailedConnection($exception);
        }

        $libraryOptions = $this->options->getLibOptions();

        try {
            $resource             = $this->applyLibraryOptions($resource, $libraryOptions);
            $this->libraryOptions = $this->mergeLibraryOptionsFromCluster($libraryOptions, $resource);
        } catch (RedisClusterException $exception) {
            throw RedisRuntimeException::fromClusterException($exception, $resource);
        }

        return $resource;
    }

    private function createRedisResource(RedisClusterOptions $options): RedisClusterFromExtension
    {
        if ($options->hasName()) {
            return $this->createRedisResourceFromName(
                $options->getName(),
                $options->getTimeout(),
                $options->getReadTimeout(),
                $options->isPersistent(),
                $options->getPassword()
            );
        }

        $password = $options->getPassword();
        if ($password === '') {
            $password = null;
        }

        return new RedisClusterFromExtension(
            null,
            $options->getSeeds(),
            $options->getTimeout(),
            $options->getReadTimeout(),
            $options->isPersistent(),
            $password
        );
    }

    /**
     * @psalm-param non-empty-string $name
     */
    private function createRedisResourceFromName(
        string $name,
        float $fallbackTimeout,
        float $fallbackReadTimeout,
        bool $persistent,
        string $fallbackPassword
    ): RedisClusterFromExtension {
        $options     = new RedisClusterOptionsFromIni();
        $seeds       = $options->getSeeds($name);
        $timeout     = $options->getTimeout($name, $fallbackTimeout);
        $readTimeout = $options->getReadTimeout($name, $fallbackReadTimeout);
        $password    = $options->getPasswordByName($name, $fallbackPassword);

        return new RedisClusterFromExtension(
            null,
            $seeds,
            $timeout,
            $readTimeout,
            $persistent,
            $password
        );
    }

    /**
     * @psalm-param array<positive-int,mixed> $options
     */
    private function applyLibraryOptions(
        RedisClusterFromExtension $resource,
        array $options
    ): RedisClusterFromExtension {
        /** @psalm-suppress MixedAssignment */
        foreach ($options as $option => $value) {
            /**
             * @see https://github.com/phpredis/phpredis#setoption
             *
             * @psalm-suppress InvalidArgument
             * @psalm-suppress MixedArgument
             */
            $resource->setOption($option, $value);
        }

        return $resource;
    }

    /**
     * @psalm-param array<positive-int,mixed> $options
     * @psalm-return array<positive-int,mixed>
     */
    private function mergeLibraryOptionsFromCluster(array $options, RedisClusterFromExtension $resource): array
    {
        foreach (RedisClusterOptions::LIBRARY_OPTIONS as $option) {
            if (array_key_exists($option, $options)) {
                continue;
            }

            /**
             * @see https://github.com/phpredis/phpredis#getoption
             *
             * @psalm-suppress InvalidArgument
             */
            $options[$option] = $resource->getOption($option);
        }

        return $options;
    }

    /**
     * @psalm-param RedisClusterOptions::OPT_* $option
     * @return mixed
     */
    public function getLibOption(int $option)
    {
        if (array_key_exists($option, $this->libraryOptions)) {
            return $this->libraryOptions[$option];
        }

        /**
         * @see https://github.com/phpredis/phpredis#getoption
         *
         * @psalm-suppress InvalidArgument
         */
        return $this->libraryOptions[$option] = $this->getResource()->getOption($option);
    }

    public function hasSerializationSupport(PluginCapableInterface $adapter): bool
    {
        /**
         * NOTE: we are not using {@see RedisClusterResourceManager::getLibOption} here
         *       as this would create a connection to redis even tho it wont be needed.
         *       Theoretically, it would be possible for upstream projects to receive the resource directly from the
         *       resource manager and then apply changes to it. As this is not the common use-case, this is not
         *       considered in this check.
         */
        $options    = $this->options;
        $serializer = $options->getLibOption(
            RedisClusterFromExtension::OPT_SERIALIZER,
            RedisClusterFromExtension::SERIALIZER_NONE
        );

        if ($serializer !== RedisClusterFromExtension::SERIALIZER_NONE) {
            return true;
        }

        /** @var iterable<PluginInterface> $plugins */
        $plugins = $adapter->getPluginRegistry();
        foreach ($plugins as $plugin) {
            if (! $plugin instanceof Serializer) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @psalm-return RedisClusterInfoType
     */
    private function info(RedisClusterFromExtension $resource): array
    {
        if ($this->options->hasName()) {
            $name = $this->options->getName();

            /** @psalm-var RedisClusterInfoType $info */
            $info = $resource->info($name);
            return $info;
        }

        $seeds = $this->options->getSeeds();
        if ($seeds === []) {
            throw new RuntimeException('Neither the node name nor any seed is configured.');
        }

        $seed = $seeds[0];
        /** @psalm-var RedisClusterInfoType $info */
        $info = $resource->info($seed);

        return $info;
    }
}
