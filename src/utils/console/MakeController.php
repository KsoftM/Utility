<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\io\FileManager;

class MakeController
{
    public static function getControllerFileName(string $fileName): ?string
    {
        return pathinfo($fileName, PATHINFO_FILENAME);
    }
    
    public static function makeControllerFile(string $controllerName, string $path, string $templatePath): void
    {
        $file = new FileManager($path);
        $className = Make::generateClassName($controllerName . '_controller');

        foreach ($file->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $name = self::getControllerFileName($value->getPath()) ?? '';

                if ($controllerName == $name) {
                    Log::BlogLog("Controller name must be unique.");
                    exit;
                }

                if ($value->contains("$className")) {
                    Log::BlogLog("One of other controller class contain this name.");
                    exit;
                }
            }
        }

        MakeTemplateFile::create($path, $className, $templatePath, [
            '{className}' => $className
        ]);
    }
}
