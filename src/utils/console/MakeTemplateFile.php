<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\io\FileManager;

class MakeTemplateFile
{
    public static function create(string $path, string $className, string $templatePath, array $replaces): bool
    {
        $file = new FileManager($path . "/$className.php");

        $data = new FileManager($templatePath);
        $data = $data->read();

        foreach ($replaces as $key => $value) {
            $data = str_replace($key, $value, $data);
        }

        if ($file->write($data, true)) {
            Log::BlogLog("$className is created successfully.");
        } else {
            Log::BlogLog("Controller file is not created...!");
            exit;
        }

        return false;
    }
}
