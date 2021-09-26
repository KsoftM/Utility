<?php

namespace ksoftm\system\utils\console;

use Closure;

class Log
{
    public static function BlogLog(string $message): void
    {
        $message = trim($message);
        $blog = '';
        $i = strlen($message);
        while (0 <= $i - 1) {
            $blog .= '-';
            $i--;
        }

        echo PHP_EOL . "$blog" . PHP_EOL;
        echo PHP_EOL . $message . PHP_EOL;
        echo PHP_EOL . "$blog" . PHP_EOL;
    }

    public static function block(Closure $callback): void
    {
        $blog = '';

        ob_start();
        call_user_func($callback);
        $d = ob_get_clean();
        $i = 0;

        foreach (explode(PHP_EOL, $d) as $value) {
            if ($i < strlen($value)) {
                $i = strlen($value);
            }
        }

        while (0 <= $i - 1) {
            $blog .= '-';
            $i--;
        }

        echo PHP_EOL . trim($blog) . PHP_EOL;
        echo PHP_EOL . trim($d) . PHP_EOL;
        echo PHP_EOL . trim($blog) . PHP_EOL;
    }
}
