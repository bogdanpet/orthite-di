<?php

namespace Orthite\DI;


class Container
{

    protected $pool = [];

    public function get($key, $params = [])
    {
        if ($this->has($key)) {
            return $this->pool[$key];
        }

        return $this->resolve($key, $params);
    }

    public function set($key, $value)
    {
        return $key;
    }

    public function has($key)
    {
        return $key;
    }

    protected function resolve($class, $params = [])
    {
        return $class;
    }

    public function call($class, $method, $params = [])
    {
        return $class;
    }
}