<?php

namespace ksoftm\system\utils;

trait StaticArgumentsTrait
{

    /**
     * array of arguments
     * 
     * @var array $args
     */
    protected static array $args = [];

    protected static function setArguments(
        string $type,
        string $alias = null,
        mixed $value = null,
        bool $overRight = false
    ) {
        if (is_null($alias)) {
            self::$args[$type][] = $value;
        } else {
            if (!isset(self::$args[$type][$alias]) || $overRight) {
                self::$args[$type][$alias] = $value;
            }
        }
    }

    protected static function getArguments($name = null): ?array
    {
        if (
            (!is_null(self::$args) &&
                array_key_exists($name, self::$args) &&
                !is_null($name))
        ) {
            return self::$args[$name];
        }
        return null;
    }

    protected static function removeArguments($name = null): bool
    {
        if (
            (!is_null(self::$args) &&
                array_key_exists($name, self::$args) &&
                !is_null($name))
        ) {
            unset(self::$args[$name]);
            return true;
        }
        return false;
    }
}
