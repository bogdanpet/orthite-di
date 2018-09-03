<?php

use Orthite\DI\Container;

if (!function_exists('container')) {
    function container($key)
    {
        return Container::getInstance()->get($key);
    }
}