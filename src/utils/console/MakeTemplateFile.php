<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\io\FileManager;

class MakeTemplateFile
{
    public static function create(string $path, string $className, string $templatePath, array $replaces): bool
    {
        $file = new FileManager($path);
        foreach ($file->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $name = Make::getFileName($value->getPath());
                $className = Make::getFileName($className);

                if ($className == $name && $value->contains(" $className ")) {
                    echo "$className, must be a unique name." . PHP_EOL . PHP_EOL;
                    // exit;
                }
            }
        }

        $file = new FileManager($path . "/$className.php");

        if (!$file->isExist()) {
            $data = new FileManager($templatePath);
            $data = $data->read();

            foreach ($replaces as $key => $value) {
                $data = str_replace($key, $value, $data);
            }

            if ($file->write($data, true)) {
                echo "$className is created successfully." . PHP_EOL . PHP_EOL;
            } else {
                echo "$className file is not created...!" . PHP_EOL . PHP_EOL;
                exit;
            }
        }

        return false;
    }
}
