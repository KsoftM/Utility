<?php

namespace ksoftm\system\utils\database;


abstract class Migration
{
    /**
     * Class constructor.
     */
    public function __construct()
    {
    }

    abstract function up(): void;

    abstract function down(): void;

    public function applyMigration(): void
    {
        $this->up();
    }

    public function applyRoleBackMigration(): void
    {
        $this->down();
        $this->up();
    }
}
