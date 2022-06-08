<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter;

use Laminas\Cache\Exception\RuntimeException;
use Laminas\Cache\Storage\Adapter\Exception\InvalidRedisClusterConfigurationException;
use Traversable;

final class RedisClusterOptions extends AdapterOptions
{
    public const LIBRARY_OPTIONS = [
        self::OPT_SERIALIZER,
        self::OPT_PREFIX,
        self::OPT_READ_TIMEOUT,
        self::OPT_SCAN,
        self::OPT_SLAVE_FAILOVER,
        self::OPT_TCP_KEEPALIVE,
        self::OPT_COMPRESSION,
        self::OPT_REPLY_LITERAL,
        self::OPT_COMPRESSION_LEVEL,
        self::OPT_NULL_MULTIBULK_AS_NULL,
    ];

    public const OPT_SERIALIZER             = 1;
    public const OPT_PREFIX                 = 2;
    public const OPT_READ_TIMEOUT           = 3;
    public const OPT_SCAN                   = 4;
    public const OPT_SLAVE_FAILOVER         = 5;
    public const OPT_TCP_KEEPALIVE          = 6;
    public const OPT_COMPRESSION            = 7;
    public const OPT_REPLY_LITERAL          = 8;
    public const OPT_COMPRESSION_LEVEL      = 9;
    public const OPT_NULL_MULTIBULK_AS_NULL = 10;

    /** @var string */
    protected $namespaceSeparator = ':';

    /** @var string */
    private $name = '';

    /** @var float */
    private $timeout = 1.0;

    /** @var float */
    private $readTimeout = 2.0;

    /** @var bool */
    private $persistent = false;

    /** @psalm-var list<non-empty-string> */
    private $seeds = [];

    /** @var string */
    private $version = '';

    /** @psalm-var array<positive-int,mixed> */
    private $libOptions = [];

    /** @var string */
    private $password = '';

    /**
     * @param array|Traversable|null|AdapterOptions $options
     * @psalm-param array<string,mixed>|Traversable<string,mixed>|null|AdapterOptions $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof AdapterOptions) {
            $options = $options->toArray();
        }

        /** @psalm-suppress InvalidArgument */
        parent::__construct($options);
        $hasName  = $this->hasName();
        $hasSeeds = $this->getSeeds() !== [];

        if (! $hasName && ! $hasSeeds) {
            throw InvalidRedisClusterConfigurationException::fromMissingRequiredValues();
        }

        if ($hasName && $hasSeeds) {
            throw InvalidRedisClusterConfigurationException::fromNameAndSeedsProvidedViaConfiguration();
        }
    }

    public function setTimeout(float $timeout): void
    {
        $this->timeout = $timeout;
        $this->triggerOptionEvent('timeout', $timeout);
    }

    public function setReadTimeout(float $readTimeout): void
    {
        $this->readTimeout = $readTimeout;
        $this->triggerOptionEvent('read_timeout', $readTimeout);
    }

    public function setPersistent(bool $persistent): void
    {
        $this->persistent = $persistent;
    }

    public function getNamespaceSeparator(): string
    {
        return $this->namespaceSeparator;
    }

    public function setNamespaceSeparator(string $namespaceSeparator): void
    {
        if ($this->namespaceSeparator === $namespaceSeparator) {
            return;
        }

        $this->triggerOptionEvent('namespace_separator', $namespaceSeparator);
        $this->namespaceSeparator = $namespaceSeparator;
    }

    public function hasName(): bool
    {
        return $this->name !== '';
    }

    /**
     * @psalm-return non-empty-string
     * @throws RuntimeException If method is called but `name` was not provided via configuration.
     */
    public function getName(): string
    {
        $name = $this->name;
        if ($name === '') {
            throw new RuntimeException('`name` is not provided via configuration.');
        }

        return $name;
    }

    /**
     * @psalm-param non-empty-string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
        $this->triggerOptionEvent('name', $name);
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function getReadTimeout(): float
    {
        return $this->readTimeout;
    }

    public function isPersistent(): bool
    {
        return $this->persistent;
    }

    /**
     * @return array<int,string>
     * @psalm-return list<non-empty-string>
     */
    public function getSeeds(): array
    {
        return $this->seeds;
    }

    /**
     * @param array<int,string> $seeds
     * @psalm-param list<non-empty-string> $seeds
     */
    public function setSeeds(array $seeds): void
    {
        $this->seeds = $seeds;

        $this->triggerOptionEvent('seeds', $seeds);
    }

    /**
     * @param non-empty-string $version
     */
    public function setRedisVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getRedisVersion(): string
    {
        return $this->version;
    }

    /**
     * @psalm-param array<positive-int,mixed> $options
     */
    public function setLibOptions(array $options): void
    {
        $this->libOptions = $options;
    }

    /**
     * @psalm-return array<positive-int,mixed>
     */
    public function getLibOptions(): array
    {
        return $this->libOptions;
    }

    /**
     * @psalm-param RedisClusterOptions::OPT_* $option
     * @param mixed $default
     * @return mixed
     */
    public function getLibOption(int $option, $default = null)
    {
        return $this->libOptions[$option] ?? $default;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @psalm-param non-empty-string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
