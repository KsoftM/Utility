<?php

namespace ksoftm\system\utils;


trait SingletonTrait
{
    private static $instance;

    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            // new self() will refer to the class that uses the trait            
            self::$instance = new self();
        }
        return self::$instance;
    }
}
