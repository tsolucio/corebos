<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Storage\Adapter\Exception\InvalidRedisClusterConfigurationException;

use function assert;
use function ini_get;
use function is_numeric;
use function is_string;
use function parse_str;

/**
 * @link https://github.com/phpredis/phpredis/blob/e9ba9ff12e74c3483f2cb54b7fc9fb7250829a2a/cluster.markdown#loading-a-cluster-configuration-by-name
 */
final class RedisClusterOptionsFromIni
{
    /** @psalm-var array<non-empty-string,list<non-empty-string>> */
    private $seedsByName;

    /** @psalm-var array<non-empty-string,float> */
    private $timeoutByName;

    /** @psalm-var array<non-empty-string,float> */
    private $readTimeoutByName;

    /** @psalm-var array<non-empty-string,string> */
    private $authenticationByName;

    public function __construct()
    {
        $seedsConfiguration = ini_get('redis.clusters.seeds');
        if (! is_string($seedsConfiguration)) {
            $seedsConfiguration = '';
        }

        if ($seedsConfiguration === '') {
            throw InvalidRedisClusterConfigurationException::fromMissingSeedsConfiguration();
        }

        $seedsByName = [];
        parse_str($seedsConfiguration, $seedsByName);
        /** @psalm-var non-empty-array<non-empty-string,list<non-empty-string>> $seedsByName */
        $this->seedsByName = $seedsByName;

        $timeoutConfiguration = ini_get('redis.clusters.timeout');
        if (! is_string($timeoutConfiguration)) {
            $timeoutConfiguration = '';
        }

        $timeoutByName = [];
        parse_str($timeoutConfiguration, $timeoutByName);
        foreach ($timeoutByName as $name => $timeout) {
            assert($name !== '' && is_numeric($timeout));
            $timeoutByName[$name] = (float) $timeout;
        }
        /** @psalm-var array<non-empty-string,float> $timeoutByName */
        $this->timeoutByName = $timeoutByName;

        $readTimeoutConfiguration = ini_get('redis.clusters.read_timeout');
        if (! is_string($readTimeoutConfiguration)) {
            $readTimeoutConfiguration = '';
        }

        $readTimeoutByName = [];
        parse_str($readTimeoutConfiguration, $readTimeoutByName);
        foreach ($readTimeoutByName as $name => $readTimeout) {
            assert($name !== '' && is_numeric($readTimeout));
            $readTimeoutByName[$name] = (float) $readTimeout;
        }

        /** @psalm-var array<non-empty-string,float> $readTimeoutByName */
        $this->readTimeoutByName = $readTimeoutByName;

        $authenticationConfiguration = ini_get('redis.clusters.auth');
        if (! is_string($authenticationConfiguration)) {
            $authenticationConfiguration = '';
        }

        $authenticationByName = [];
        parse_str($authenticationConfiguration, $authenticationByName);
        /** @psalm-var array<non-empty-string,string> $authenticationByName */

        $this->authenticationByName = $authenticationByName;
    }

    /**
     * @psalm-param non-empty-string $name
     * @return array<int,string>
     * @psalm-return list<non-empty-string>
     */
    public function getSeeds(string $name): array
    {
        $seeds = $this->seedsByName[$name] ?? [];
        if (! $seeds) {
            throw InvalidRedisClusterConfigurationException::fromMissingSeedsForNamedConfiguration($name);
        }

        return $seeds;
    }

    /**
     * @psalm-param non-empty-string $name
     */
    public function getTimeout(string $name, float $fallback): float
    {
        return $this->timeoutByName[$name] ?? $fallback;
    }

    /**
     * @psalm-param non-empty-string $name
     */
    public function getReadTimeout(string $name, float $fallback): float
    {
        return $this->readTimeoutByName[$name] ?? $fallback;
    }

    /**
     * @psalm-param non-empty-string $name
     */
    public function getPasswordByName(string $name, string $fallback): string
    {
        return $this->authenticationByName[$name] ?? $fallback;
    }
}
