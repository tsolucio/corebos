<?php

declare(strict_types=1);

namespace Laminas\Cache\Storage\Adapter\Exception;

use Laminas\Cache\Exception\RuntimeException as LaminasCacheRuntimeException;
use RedisCluster;
use RedisClusterException;
use Throwable;

final class RedisRuntimeException extends LaminasCacheRuntimeException
{
    public static function fromClusterException(RedisClusterException $exception, RedisCluster $redis): self
    {
        $message = $redis->getLastError() ?? $exception->getMessage();

        return new self($message, (int) $exception->getCode(), $exception);
    }

    public static function fromFailedConnection(Throwable $exception): self
    {
        return new self(
            'Could not establish connection',
            (int) $exception->getCode(),
            $exception
        );
    }
}
