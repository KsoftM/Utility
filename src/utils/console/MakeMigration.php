<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\io\FileManager;

class MakeMigration
{
    protected const SPECIAL_SEPARATOR = '___';

    public static function getMigrationFileName(string $fileName): ?string
    {
        $path = (explode(
            self::SPECIAL_SEPARATOR,
            pathinfo($fileName, PATHINFO_FILENAME),
            2
        ) + [null, null])[1];

        return str_replace('_', '', ucwords(
            $path,
            '_'
        ));
    }

    public static function createUniqueFileName(string $migrationName): string
    {
        return date_create()->format('Y_m_d_G_i_s_u') . self::SPECIAL_SEPARATOR .
            $migrationName . '_migration';
    }

    public static function makeMigrationFile(string $migrationName, string $path, string $templatePath): void
    {
        $file = new FileManager($path);
        $className = Make::generateClassName($migrationName . '_migration');

        foreach ($file->getDirectoryFiles(true) as $value) {
            if ($value instanceof FileManager) {
                $name = self::getMigrationFileName($value->getPath()) ?? '';


                if (!empty($migrationName) && $migrationName == $name) {
                    Log::BlogLog("Migration name must be unique.");
                    exit;
                }

                if ($className != false && $value->contains(" $className ")) {
                    Log::BlogLog("One of other migration class contain this name.");
                    exit;
                }
            }
        }

        $uniqueFileName = self::createUniqueFileName($migrationName);
        $className = Make::generateClassName($migrationName . '_migration');

        MakeTemplateFile::create($path, $uniqueFileName, $templatePath, [
            '{className}' => $className
        ]);
    }
}
