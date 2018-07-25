<?php

namespace Orthite\DI;


class Container
{

    protected $pool = [];

    public function get($key)
    {
        return $key;
    }

    public function set($key, $value)
    {
        return $key;
    }

    public function has($key)
    {
        return $key;
    }

    public function resolve($class, $params = [])
    {
        return $class;
    }

    public function resolveMethod($class, $method, $params = [])
    {
        return $class;
    }
}