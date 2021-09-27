<?php

namespace ksoftm\system\utils;

use Closure;

class FormMaker
{
    public function open(
        string $name,
        string $method = 'post',
        string $action = '',
        array $extras = [],
        string $token = null,
        Closure $callback = null
    ): string {
        $tmp = [];
        foreach ($extras ?? [] as $key => $value) {
            $tmp[] = $key . '=' . $value;
        }
        $tmp = implode(' ', $tmp);
        $output[] = "<form name='$name' action='$action' method='$method' $tmp >";
        $output[] = $token;

        if (!empty($callback)) {
            $output[] = call_user_func($callback);
        }

        $output[] = "</form>";

        return implode('\n', $output);
    }

    public function input(string $type, string $name, array $extras = []): string
    {
        $tmp = [];
        foreach ($extras ?? [] as $key => $value) {
            $tmp[] = $key . '=' . $value;
        }

        $output = "<input type='$type' name='$name' $tmp />";

        return $output;
    }

    public function label(string $data, string $for, array $extras = []): string
    {
        $tmp = [];
        foreach ($extras ?? [] as $key => $value) {
            $tmp[] = $key . '=' . $value;
        }

        $output[] = "<label for='$for' $tmp>";
        $output[] = $data;
        $output[] = "</label>";

        return implode('\n', $output);
    }
}
