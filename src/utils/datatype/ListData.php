<?php

namespace ksoftm\system\utils\datatype;

class ListData
{

    /** @var array $args arguments of the dictionary. */
    protected array $args = [];

    /**
     * Class constructor.
     */
    public function __construct(array $data = [])
    {
        $this->args = array_values($data);
    }

    public function clean(): void
    {
        $this->args = [];
    }

    public function add($value): bool
    {
        if (!is_null($value)) {
            $this->args[] = $value;
            return true;
        }
        return false;
    }

    public function remove($value): bool
    {
        if (!is_null($value) && $this->have($value)) {
            foreach ($this->args as $key => $val) {
                if ($val == $value) {
                    unset($this->args[$key]);
                    sort($this->args);
                    return true;
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

        return $d;
    }

    public function have($data): bool
    {
        return in_array($data, $this->args);
    }

    public function haveKey($key): bool
    {
        return array_key_exists($key, $this->args);
    }

    public function get($key): mixed
    {
        if (!is_null($key) && $this->haveKey($key)) {
            return $this->args[$key];
        }
        return false;
    }

    public function __get(string $data)
    {
        return $this->get($data);
    }

    public function __set(string $key, $value)
    {
        $this->add($key, $value);
    }
}
