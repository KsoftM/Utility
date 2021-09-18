<?php

namespace ksoftm\system\utils;

trait ArgumentsTrait
{

    /**
     * array of arguments
     * 
     * @var array $args
     */
    protected array $args = [];

    protected function setArguments(
        string $type,
        string $alias = null,
        mixed $value = null,
        bool $overRight = false
    ) {
        if (is_null($alias)) {
            $this->args[$type][] = $value;
        } else {
            if (!isset($this->args[$type][$alias]) || $overRight) {
                $this->args[$type][$alias] = $value;
            }
        }
    }

    protected function getArguments($name = null): ?array
    {
        if (
            (!is_null($this->args) &&
                array_key_exists($name, $this->args) &&
                !is_null($name))
        ) {
            return $this->args[$name];
        }
        return null;
    }

    protected function removeArguments(string $name = null, string $alias): bool
    {
        if (
            (!is_null($this->args) &&
                array_key_exists($name, $this->args) &&
                !is_null($name))
        ) {
            unset($this->args[$name][$alias]);
            return true;
        }
        return false;
    }
}
