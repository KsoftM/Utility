<?php

namespace ksoftm\system\utils\console;


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
}
