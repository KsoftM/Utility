<?php

namespace ksoftm\system\utils\datatype;


class Dictionary
{
    /** @var array $args arguments of the dictionary. */
    protected array $args = [];

    /**
     * Class constructor.
     */
    public function __construct(array $data = [])
    {
        $this->args = $data;
    }

    public function clean(): void
    {
        $this->args = [];
    }

    public function add($key, $value): bool
    {
        if (!is_null($key) && !$this->haveKey($key)) {
            $this->args[$key] = $value;
            return true;
        }
        return false;
    }

    public function removeKey($key): bool
    {
        if (!is_null($key) && $this->haveKey($key)) {
            unset($this->args[$key]);
            return true;
        }
        return false;
    }

    public function removeValue($value): bool
    {
        if (!is_null($value) && $this->haveValue($value)) {
            foreach ($this->args as $key => $val) {
                if ($val == $value) {
                    unset($this->args[$key]);
                    return true;
                }
            }
        }
        return false;
    }

    public function getValue($key): mixed
    {
        if (!is_null($key) && $this->haveKey($key)) {
            return $this->args[$key];
        }
        return false;
    }

    public function getKey($value): mixed
    {
        if (!is_null($value) && $this->haveValue($value)) {
            foreach ($this->args as $key => $val) {
                if ($val == $value) {
                    return $key;
                }
            }
        }
        return false;
    }

    public function getEach(callable $callback): mixed
    {
        foreach ($this->args as $key => $value) {
            $d[] = call_user_func($callback, $value);
        }

        return $d ??  false;
    }

    public function haveKey($key): bool
    {
        return array_key_exists($key, $this->args);
    }

    public function haveValue($value): bool
    {
        return in_array($value, $this->args);
    }
}
