<?php

namespace ksoftm\system\utils;

use ksoftm\system\utils\html\BuildMixer;

class View
{
    private static ?string $resourcesPath = null;
    private static array $landDirectory = [];

    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    // TODO view must be configure
    public static function config(string $resourcesPath, array $landDirectory): void
    {
        self::$resourcesPath = $resourcesPath;
        self::$landDirectory = $landDirectory;
    }

    public function view(string $path, array $data = []): string
    {
        return BuildMixer::build(
            self::$resourcesPath,
            $path,
            $data,
            self::$landDirectory,
        );
    }
}
