<?php

namespace Core;

use Exception;
use ReflectionClass;
use RefllectionParameter;

class Container
{
    private $bindings = [];
    private $instances = [];

    public function bind($abstract, $concrete = null)
    {
        if ($concrete === null) {
            $concrete = $abstract;
        }

        $this->bindings[$abstract] = $concrete;
    }

    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete);
        $this->instances[$abstract] = null;
    }

    public function get($abstract)
    {
        // Return singleton instance if exists
        if (isset($this->instances[$abstract]) && $this->instances[$abstract] !== null) {
            return $this->instances[$abstract];
        }

        // Resolve the binding
        if (!isset($this->bindings[$abstract])) {
            return $this->resolve($abstract);

        }

        $concrete = $this->bindings[$abstract];

        if ($concrete instanceof \Closure) {
            $object = $concrete();
        } else {
            $object = $this->resolve($concrete);
        }

        // Store singleton instance
        if (isset($this->instances[$abstract])) {
            $this->instances[$abstract] = $object;
        }

        return $object;
    }

    private function resolve($class)
    {
        $reflector = new ReflectionClass($class);

        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$class} is not instantiable");
        }

        $constructor = $reflector->getConstructor();

        if ($constructor === null) {
            return new $class;
        }

        $parameters = $constructor->getParameters();
        $dependencies = $this->resolveDependencies($parameters);

        return $reflector->newInstanceArgs($dependencies);
    }

    private function resolveDependencies(array $parameters)
    {
        $dependencies = [];

        foreach ($parameters as $parameter) {
            $dependency = $parameter->getClass();

            if ($dependency === null) {
                if ($parameter->isDefaultValueAvailable()) {
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Cannot resolve parameter {$parameter->name}");
                }
            } else {
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }


}