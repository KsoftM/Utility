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
    public const FUNC_MIGRATION = 'make:migration';
    public const FUNC_CONTROLLER = 'make:controller';
    public const FUNC_MODEL = 'make:model';
    public const FUNC_MIGRATE = 'migrate';

    public const FUNC_SHORT = [
        '-m', '-c', '-r'
    ];

    protected const PATH = [
        self::FUNC_MIGRATION => '/migration',
        self::FUNC_CONTROLLER => '/controller',
        self::FUNC_MODEL => '/model',
    ];

    protected const TMPL = [
        self::FUNC_MIGRATION => '/template/migration.tmpl',
        self::FUNC_CONTROLLER => '/template/controller.tmpl',
        self::FUNC_MODEL => '/template/model.tmpl',
    ];

    /**
     * Class constructor.
     */
    private function __construct()
    {
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
        if (!empty($args))
            $optional = $args;

        if (empty($func)) {
            Log::BlogLog("Invalid argument passed.");
            exit;
        }

        if (!empty($optional) && is_array($optional)) {
            $optional = $optional[0];
        }

        switch ($func) {
            case Make::FUNC_MIGRATION:
                if (!empty($optional) && self::IsValidName($optional))
                    self::migration($optional, $root);
                else
                    Log::BlogLog("Invalid argument passed.");

                break;
            case Make::FUNC_CONTROLLER:
                if (!empty($optional) && self::IsValidName($optional))
                    self::controller($optional, $root);
                else
                    Log::BlogLog("Invalid argument passed.");

                break;
            case Make::FUNC_MODEL:
                if (!empty($optional) && self::IsValidName($optional))
                    self::model($optional, $root);
                else
                    Log::BlogLog("Invalid argument passed.");

                break;
            case Make::FUNC_MIGRATE:
                self::migrate($root, $optional ?? false);

                break;
            default:
                Log::BlogLog('Invalid commend.');
                break;
        }

        //TODO: optional parameter build


        // Log::BlogLog($func . PHP_EOL . $name . PHP_EOL . implode(', ', $optional));
    }

    public static function IsValidName(string $Name): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{3,60}$/', $Name) === false ? false : true;
    }

    public static function migration(string|false $name, string|false $root): void
    {
        MakeMigration::makeMigrationFile(
            $name,
            $root . self::PATH[self::FUNC_MIGRATION],
            __DIR__ . self::TMPL[self::FUNC_MIGRATION]
        );
    }

    public static function migrate(string|false $root, array|string|false $optional): void
    {
        $path = $root . self::PATH[self::FUNC_MIGRATION];

        Log::block(function () use ($optional, $path) {
            if ($optional == '-r') {
                ApplyMigration::applyRoleBackMigration($path);
            } else {
                ApplyMigration::applyMigration($path);
            }
        });
    }

    public static function controller(string|false $name, string|false $root): void
    {
        MakeController::makeControllerFile(
            $name,
            $root . self::PATH[self::FUNC_CONTROLLER],
            __DIR__ . self::TMPL[self::FUNC_CONTROLLER]
        );
    }

    public static function model(): void
    {
        // TODO make model
        // MakeModel::makeModelFile(
        //     $name,
        //     $root . self::PATH[self::FUNC_MODEL],
        // __DIR__ . self::TMPL[self::FUNC_MODEL]
        // );
    }
}
