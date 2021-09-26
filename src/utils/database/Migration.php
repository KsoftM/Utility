<?php

namespace ksoftm\system\utils\database;

use ksoftm\system\utils\console\Log;

abstract class Migration
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    public abstract function up(): void;

    public abstract function down(): void;

    public function applyMigration(string $name): void
    {
        $this->up();
        echo $name . ' is upped successful.' . PHP_EOL;
    }

    public function applyRoleBackMigration(string $name): void
    {
        $this->down();
        echo $name . ' is downed successful.' . PHP_EOL;
        $this->up();
        echo $name . 'is upped successful.' . PHP_EOL . PHP_EOL;
    }
}
