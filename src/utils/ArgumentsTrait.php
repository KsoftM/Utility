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
    ): void {
        if (is_null($alias)) {
            $this->args[$name][] = $data;
        } else {
            if ($overRight || !($this->have($name, $alias))) {
                $this->args[$name][$alias] = $data;
            }
        }
    }

    public function have(string $name, string $alias = null): bool
    {
        if (!is_null($name)) {
            if (!is_null($alias) && array_key_exists($name, $this->getAll())) {
                return array_key_exists($alias, $this->getAll()[$name]);
            } else {
                return array_key_exists($name, $this->getAll());
            }
        }
        return false;
    }

    public function getArguments(string $name, string $alias = null): mixed
    {
        if ($this->have($name, $alias) && !is_null($alias)) {
            return $this->getAll()[$name][$alias];
        } elseif ($this->have($name)) {
            return $this->getAll()[$name];
        }
        return null;
    }

    protected function getAll(): array
    {
        return !empty($this->args ?? []) ? $this->args : [];
    }

    protected function clean(): void
    {
        $this->args = [];
    }

    public function removeArguments(string $name, string $alias = null): bool
    {
        if ($this->haveName($name)) {
            if (is_null($alias)) {
                unset($this->args[$name]);
            } else {
                unset($this->args[$name][$alias]);
            }
            return true;
        }
        return false;
    }
}
