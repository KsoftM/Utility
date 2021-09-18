<?php

namespace ksoftm\system\utils\database;

use ksoftm\system\utils\io\FileManager;

class ApplyMigration
{
    public static function applyMigration(string $migrationDir): void
    {
        $f = new FileManager($migrationDir);

        foreach ($f->getDirectoryFileNames(true) as $value) {
            if ($value instanceof FileManager) {
                $class = MakeMigration::getClassNameUsingFile($value->getPath());

                if (!empty($class)) {

                    $value->requireOnce();

                    $class = new $class();
                    if ($class instanceof Migration) {
                        $class->applyMigration();
                    }
                }
            }
        }
    }

    public static function applyRoleBackMigration(string $migrationDir): void
    {
        $f = new FileManager($migrationDir);

        foreach ($f->getDirectoryFileNames(true) as $value) {
            if ($value instanceof FileManager) {
                $class = MakeMigration::getClassNameUsingFile($value->getPath());

                if (!empty($class)) {

                    $value->requireOnce();

                    $class = new $class();
                    if ($class instanceof Migration) {
                        $class->applyRoleBackMigration();
                    }
                }
            }
        }
    }
}
