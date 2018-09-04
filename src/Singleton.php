<?php


namespace Orthite\DI;


abstract class Singleton
{

    protected static $instances = [];

    protected function __construct()
    {
    }

    public static function getInstance() {
        if (empty(static::$instances[static::class])) {
            $class = static::class;
            static::$instances[static::class] = new $class;
        }

        return static::$instances[static::class];
    }
}