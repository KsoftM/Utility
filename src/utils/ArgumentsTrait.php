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
        string $name,
        mixed $data,
        string $alias = null,
        bool $overRight = false
    ) {
        if (empty($alias)) {
            $this->args[$name][] = $data;
        } else {
            if ($overRight || !($this->have($name, $alias))) {
                $this->args[$name][$alias] = $data;
            }
        }
        sort($this->args);
    }

    protected function have(string $name, string $alias = null): bool
    {
        if (!empty($name)) {
            if (!empty($alias)) {
                return in_array($alias, $this->getAll()[$name]);
            } else {
                return in_array($name, $this->getAll());
            }
        }
        return false;
    }

    protected function getArguments(string $name, string $alias = null): ?array
    {
        if ($this->haveName($name) && !empty($alias)) {
            return $this->getAll()[$name];
        } elseif ($this->have($name, $alias)) {
            return $this->getAll()[$name][$alias];
        }
        return null;
    }

    protected function getAll(): array
    {
        return !empty($this->args ?? []) ?: [];
    }

    protected function removeArguments(string $name, string $alias = null): bool
    {
        if ($this->haveName($name)) {
            if (empty($alias)) {
                unset($this->args[$name]);
                sort($this->args);
            } else {
                unset($this->args[$name][$alias]);
                sort($this->args);
            }
            return true;
        }
        return false;
    }
}
