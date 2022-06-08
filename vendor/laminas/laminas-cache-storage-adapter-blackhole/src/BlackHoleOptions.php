<?php

/**
 * @see       https://github.com/laminas/laminas-cache for the canonical source repository
 * @copyright https://github.com/laminas/laminas-cache/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-cache/blob/master/LICENSE.md New BSD License
 */

namespace Laminas\Cache\Storage\Adapter;

final class BlackHoleOptions extends AdapterOptions
{
    /**
     * Flag to optionally allow PSR compatibility.
     * This flag is necessary due to the fact that providing proper PSR support without BC
     * breaks wont be possible otherwise.
     *
     * @var bool
     */
    protected $psr = false;

    /**
     * @internal
     *
     * @return bool
     */
    public function isPsrCompatible()
    {
        return $this->psr;
    }

    /**
     * @internal
     *
     * @param bool $psr
     * @return void
     */
    public function setPsr($psr)
    {
        $this->psr = (bool) $psr;
    }
}
