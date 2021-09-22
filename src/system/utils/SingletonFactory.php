<?php

namespace ksoftm\system\utils;

/**
 * SingletonFactory instance base class
 */
abstract class SingletonFactory
{
    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    /**
     * create the SingletonFactory instance
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
     * get the SingletonFactory instance
     *
     * @return self
     */
    public abstract static function getInstance(): self;
}
