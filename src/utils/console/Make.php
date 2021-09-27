<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\database\ApplyMigration;
use ksoftm\system\utils\database\Migration;
use ksoftm\system\utils\io\FileManager;
use ksoftm\system\utils\validator\MegaValid;
use ksoftm\system\utils\validator\MegRule;
use PDO;

class Make
{
    protected const SPECIAL_SEPARATOR = '___';

    public const FUNC_MIGRATION = 'make:migration'; // -a
    public const FUNC_CONTROLLER = 'make:controller'; // -c
    public const FUNC_MODEL = 'make:model'; // -m
    public const FUNC_MIGRATE = 'migrate'; // [-r]

    public const FUNC_SHORT = [
        self::FUNC_MIGRATION => '-a',
        self::FUNC_CONTROLLER => '-c',
        self::FUNC_MODEL => '-m',
        self::FUNC_MIGRATE => '-r'
    ];

    protected static array $PATH = [];

    protected static array $TEMPLATE = [
        self::FUNC_MIGRATION => '/template/migration.template',
        self::FUNC_CONTROLLER => '/template/controller.template',
        self::FUNC_MODEL => '/template/model.template',
    ];

    /**
     * Class constructor.
     */
    protected function __construct()
    {
    }

    public static function initPath(
        array $appPath = [
            Make::FUNC_MIGRATION => '/migration',
            Make::FUNC_CONTROLLER => '/controller',
            Make::FUNC_MODEL => '/model',
        ]
    ): void {
        self::$PATH = $appPath;
    }

    public static function generateClassName(string $fileName): string|false
    {
        $regOut = MegaValid::validate([[$fileName, MegRule::new()->userName()]]);

        if ($regOut) {
            if (strpos($fileName, '_')) {
                return str_replace('_', '', ucwords($fileName, '_') ?? '');
            } else {
                return ucwords($fileName);
            }
        }
        return false;
    }

    public static function process($args, $root): void
    {
        echo PHP_EOL;

        if (!empty($args))
            $func = array_shift($args);
        if (!empty($args) && is_array($args)) {
            $optional = array_shift($args);
        }

        if (empty($func) || !in_array($func, [
            self::FUNC_MIGRATION,
            self::FUNC_CONTROLLER,
            self::FUNC_MODEL,
            self::FUNC_MIGRATE
        ])) {
            Log::BlogLog("Invalid argument function passed.");
            exit;
        }
        if (!empty($optional) && is_array($optional)) {
            $optional = array_shift($optional);
        } else {
            $optional = $optional ?? [];
        }

        log::block(function () use ($optional, $func, $root, $args) {
            if (
                $func == Make::FUNC_MIGRATION ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MIGRATION], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::migration($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }
            if (
                $func == Make::FUNC_CONTROLLER ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_CONTROLLER], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::controller($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }
            if (
                $func == Make::FUNC_MODEL ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MODEL], $args))
            ) {
                if (!empty($optional) && self::IsValidName($optional))
                    self::model($optional, $root);
                else
                    echo "Invalid argument passed." . PHP_EOL;
            }
            if (
                $func == Make::FUNC_MIGRATE ||
                (is_array($args) &&
                    in_array(self::FUNC_SHORT[self::FUNC_MIGRATE], $args))
            ) {
                self::migrate($root, $optional ?? false);
            }
        });
    }

    public static function IsValidName(string $Name): bool
    {
        return MegaValid::validate([[$Name, MegRule::new()->userName()]]);
    }


    public static function migrate(string|false $root, array|string|false $optional): void
    {
        $path = $root . self::$PATH[self::FUNC_MIGRATION];

        if ($optional == '-r') {
            ApplyMigration::applyRoleBackMigration($path);
        } else {
            ApplyMigration::applyMigration($path);
        }
    }

    public static function migration(string|false $migrationName, string|false $root): void
    {
        $path = $root . self::$PATH[self::FUNC_MIGRATION];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MIGRATION];

        $className = Make::generateClassName($migrationName . '_migration');
        $uniqueFileName = self::createUniqueFileName($migrationName);

        MakeTemplateFile::create($path, $className, $uniqueFileName, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function controller(string|false $controllerName, string|false $root): void
    {
        $path = $root . self::$PATH[self::FUNC_CONTROLLER];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_CONTROLLER];
        $className = Make::generateClassName($controllerName . '_controller');

        MakeTemplateFile::create($path, $className, $className, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function model(string|false $modelName, string|false $root): void
    {
        $path =  $root . self::$PATH[self::FUNC_MODEL];
        $templatePath = __DIR__ . self::$TEMPLATE[self::FUNC_MODEL];
        $className = Make::generateClassName($modelName . "_model");

        MakeTemplateFile::create($path, $className, $className, $templatePath, [
            '{className}' => $className
        ]);
    }

    public static function getFileName(string $fileName): ?string
    {
        $path = (explode(
            self::SPECIAL_SEPARATOR,
            pathinfo($fileName, PATHINFO_FILENAME),
            2
        ) + [null, null])[1];

        return str_replace('_', '', ucwords(
            $path,
            '_'
        )) ?: pathinfo($fileName, PATHINFO_FILENAME);
    }

    public static function createUniqueFileName(string $migrationName): string
    {
        return date_create()->format('Y_m_d_G_i_s_u') . self::SPECIAL_SEPARATOR .
            $migrationName . '_migration';
    }
}
