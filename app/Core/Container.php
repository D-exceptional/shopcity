<?php
namespace App\Core;

class Container
{
    private array $instances = [];

    public function get(string $class)
    {
        if (isset($this->instances[$class])) {
            return $this->instances[$class];
        }

        if (!class_exists($class)) {
            throw new \Exception("Class {$class} not found");
        }

        $reflection = new \ReflectionClass($class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            $instance = new $class();
        } else {
            $dependencies = [];
            foreach ($constructor->getParameters() as $param) {
                $type = $param->getType()->getName();
                if ($type) {
                    $dependencies[] = $this->get($type); // recursive resolution
                } else {
                    $dependencies[] = null;
                }
            }
            $instance = $reflection->newInstanceArgs($dependencies);
        }

        $this->instances[$class] = $instance;
        return $instance;
    }
}
