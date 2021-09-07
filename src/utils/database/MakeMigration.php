<?php

namespace ksoftm\utils\database;

use ksoftm\utils\console\Log;
use ksoftm\utils\io\FileManager;

class MakeMigration
{
    protected const SPECIAL_SEPARATOR = '___';

    protected static function IsValidFileName(string $fileName): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{3,60}$/', $fileName) === false ? false : true;
    }

    protected static function findFileClassName(string $fileName): string
    {
        return str_replace('_', '', ucwords($fileName, '_') ?? '');
    }

    protected static function createUniqueFileName(string $migrationName): string
    {
        return date_create()->format('Y_m_d_G_i_s_u') . self::SPECIAL_SEPARATOR . $migrationName;
    }

    protected static function getMigrationFileName(string $fileName): string
    {
        return (explode(self::SPECIAL_SEPARATOR, pathinfo($fileName, PATHINFO_FILENAME), 2) + [null, null])[1];
    }

    public static function getClassNameUsingFile(string $filePath): string
    {
        return self::findFileClassName(self::getMigrationFileName($filePath) ?? '');
    }

    public static function makeMigrationFile(string $nameOfMigration, string $pathOfMigration): void
    {
        if (!self::IsValidFileName($nameOfMigration)) {
            Log::BlogLog("Migration name is invalid.");
            exit;
        }

        $file = new FileManager($pathOfMigration);

        foreach ($file->getDirectoryFileNames(true) as $value) {
            if ($value instanceof FileManager) {
                $name = self::getMigrationFileName($value->getPath()) ?? '';
                $className = self::findFileClassName($nameOfMigration);

                if ($nameOfMigration == $name) {
                    Log::BlogLog("Migration name must be unique.");
                    exit;
                }

                if ($value->contains("$className")) {
                    Log::BlogLog("One of other migration class contain this name.");
                    exit;
                }
            }
        }

        $uniqueFileName = self::createUniqueFileName($nameOfMigration);
        $className = self::findFileClassName($nameOfMigration);

        $file = new FileManager($pathOfMigration . "/$uniqueFileName.php");

        if ($file->write("<?php

use ksoftm\utils\database\Migration;

class $className extends Migration
{

    public function up(): void
    {
        echo '$className Migration up. . PHP_EOL';
    }

    public function down(): void
    {
        echo '$className Migration down. . PHP_EOL';
    }
}", true)) {
            Log::BlogLog("Migration file $uniqueFileName is created successfully.");
        } else {
            Log::BlogLog("Migration file is not created...!");
            exit;
        }
    }
}
