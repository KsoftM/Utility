<?php

namespace ksoftm\system\utils\datatype;

class ListData
{

    /** @var array $args arguments of the dictionary. */
    protected array $args = [];

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->args = [];
    }

    public function clean(): void
    {
        $this->args = [];
    }

    public function add($value): bool
    {
        if (!is_null($value) && !$this->have($value)) {
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

    public function have($value): bool
    {
        return in_array($value, $this->args);
    }

    public function get($key): mixed
    {
        if (!is_null($key) && $this->have($key)) {
            return $this->args[$key];
        }
        return false;
    }
}
