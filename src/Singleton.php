<?php


namespace Orthite\DI;


abstract class Singleton
{

    protected static $instance = null;

    protected function __construct()
    {
    }

    public static function getInstance() {
        if (static::$instance == null) {
            $class = static::class;
            static::$instance = new $class;
        }

        return static::$instance;
    }
}