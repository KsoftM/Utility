<?php

namespace ksoftm\system\utils\console;

use ksoftm\system\utils\validator\MegaValid;
use ksoftm\system\utils\validator\MegRule;

class Make
{
    public const FUNC_MIGRATION = 'make:migration';
    public const FUNC_CONTROLLER = 'make:controller';
    public const FUNC_MODEL = 'make:model';

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

        $func = array_shift($args);
        $name = array_shift($args);
        $optional = $args;

        if (!self::IsValidName($name)) {
            Log::BlogLog("Migration name is invalid.");
            exit;
        }

        // $className = Make::generateClassName($name);

        switch ($func) {
            case Make::FUNC_MIGRATION:
                self::migration($name, $root);
                break;
            case Make::FUNC_CONTROLLER:
                self::controller($name, $root);
                break;
            case Make::FUNC_MODEL:
                self::model($name, $root);
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

    public static function migration(string $name, string $root): void
    {
        MakeMigration::makeMigrationFile(
            $name,
            $root . self::PATH[self::FUNC_MIGRATION],
            __DIR__ . self::TMPL[self::FUNC_MIGRATION]
        );
    }

    public static function controller(string $name, string $root): void
    {
        MakeController::makeControllerFile(
            $name,
            $root . self::PATH[self::FUNC_CONTROLLER],
            __DIR__ . self::TMPL[self::FUNC_CONTROLLER]
        );
    }

    public static function model(): void
    {
        //TODO make model
        // MakeModel::makeModelFile(
        //     $name,
        //     $root . self::PATH[self::FUNC_MODEL],
        // __DIR__ . self::TMPL[self::FUNC_MODEL]
        // );
    }
}
