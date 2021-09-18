<?php

namespace ksoftm\system\utils\html;


class MixResult
{
    protected mixed $template = null;
    protected mixed $data = null;
    protected mixed $name = null;
    protected mixed $src = null;

    /**
     * Class constructor.
     */
    public function __construct(mixed $template = null, mixed $data = null, mixed $name = null, mixed $src = null)
    {
        $this->template = $template;
        $this->data = $data;
        $this->name = $name;
        $this->src = $src;
    }

    public function getTemplate(): mixed
    {
        return empty($this->template) ? false : $this->template;
    }

    public function getData(): mixed
    {
        return empty($this->data) ? false : $this->data;
    }

    public function getName(): mixed
    {
        return empty($this->name) ? false : $this->name;
    }

    public function getSrc(): mixed
    {
        return empty($this->src) ? false : $this->src;
    }
}
