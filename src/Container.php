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
        $this->pool[$key] = $value;
    }

    public function has($key)
    {
        return array_key_exists($key, $this->pool);
    }

    protected function resolve($class, $params = [])
    {
        // Create a reflection of a class
        $reflection = new \ReflectionClass($class);

        // Check if class is instantiable
        if (!$reflection->isInstantiable()) {
            // TODO: Add exception
            die('Class is not instantiable');
        }

        // Get class constructor
        $constructor = $reflection->getConstructor();

        // If constructor is null (not exists) proceed with simple instantiation
        // And put the object in pool for future use
        if (is_null($constructor)) {
            $instance = $reflection->newInstance();
            $this->set($class, $instance);
            return $instance;
        }

        // Otherwise get constructor params
        $constructorParams = $constructor->getParameters();

        // Try to resolve them and return the new instance with args
        $dependencies = $this->resolveDependencies($constructorParams, $params);

        return $reflection->newInstanceArgs($dependencies);
    }

    private function resolveDependencies($constructorParams, $params)
    {
        $dependencies = [];

        foreach ($constructorParams as $param) {
            // Check if the value is passed through $params
            // This value should override the default
            if (array_key_exists($param->name, $params)) {
                $dependencies[] = $params[$param->name];
            } else {
                // Otherwise try to resolve it as class using type hinting
                $dep = $param->getClass();

                // If it is a class try to resolve it
                if ($dep !== null) {
                    $dependencies[] = $this->get($dep->name);
                } else {
                    // Otherwise check for default value
                    if ($param->isDefaultValueAvailable()) {
                        // get default value of parameter
                        $dependencies[] = $param->getDefaultValue();
                    } else {
                        // TODO: Add exception
                        die('Can\'t resolve parameter ' . $param->name);
                    }
                }
            }
        }

        return $dependencies;
    }

    public function call($class, $method, $params = [])
    {
        $object = $this->get($class, $params);

        if (method_exists($object, $method)) {
            $reflection = new \ReflectionMethod($class, $method);

            if (!$reflection->isPublic()) {
                // TODO: Add exception
                die('Method ' . $class . '::' . $method . ' is not accessible.');
            }

            $methodParams = $reflection->getParameters();

            if (empty($methodParams)) {
                return $reflection->invoke($object);
            }

            $dependencies = $this->resolveDependencies($methodParams, $params);

            return $reflection->invokeArgs($object, $dependencies);
        } else {
            // TODO: Add exception
            die('Method ' . $class . '::' . $method . ' does not exist.');
        }
    }
}