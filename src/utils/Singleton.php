<?php

namespace ksoftm\utils;

/**
 * singleton instance base class
 */
abstract class Singleton
{
    /**
     * Class constructor.
     */
    protected abstract function __construct();

    /**
     * create the singleton instance
     *
     * @param mixed $base
     * @param mixed $class
     *
     * @return mixed
     */
    protected static function init(mixed &$base, mixed $class): mixed
    {
        if (!isset($base)) {
            $base = new $class();
        }

        return $base;
    }

    /**
     * get the singleton instance
     *
     * @return self
     */
    public abstract static function getInstance(): self;
}
