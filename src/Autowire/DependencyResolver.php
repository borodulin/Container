<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;

class DependencyResolver
{
    /**
     * @var AutowireItemProvider
     */
    private $itemProvider;

    public function __construct(AutowireItemProvider $itemProvider)
    {
        $this->itemProvider = $itemProvider;
    }

    public function resolve($id): object
    {
        if ($this->itemProvider->isResolved($id)) {
            return $this->itemProvider->getResolved($id);
        }
        $this->itemProvider->tryResolve($id);
        if (class_exists($id)) {
            $reflection = new \ReflectionClass($id);
            if (!$reflection->isInstantiable()) {
                throw new ContainerException("$id is not instantiable.");
            }
            $args = null;
            $constructor = $reflection->getConstructor();
            if (null !== $constructor) {
                $args = $this->resolveConstructor($constructor);
            }
            $instance = $args ? $reflection->newInstanceArgs($args) : $reflection->newInstance();
            $this->itemProvider->setResolved($id, $instance);

            return $instance;
        } else {
            throw new NotFoundException($id);
        }
    }

    private function resolveConstructor(\ReflectionMethod $constructor): array
    {
        $result = [];
        foreach ($constructor->getParameters() as $parameter) {
            if ($parameter->isVariadic()) {
                if ($parameter->getClass()) {
                    foreach ($this->findAllImplementors($parameter->getClass()->getName()) as $implementor) {
                        $result[] = $implementor;
                    }
                } else {
                    $result[] = null;
                }
            } elseif ($parameter->isDefaultValueAvailable()) {
                $result[] = $parameter->getDefaultValue();
            } elseif ($parameter->isOptional()) {
                $result[] = null;
            } elseif ($parameter->getClass()) {
                $className = $parameter->getClass()->getName();
                if ($this->itemProvider->getContainer()->has($className)) {
                    $result[] = $this->itemProvider->getContainer()->get($className);
                } else {
                    $result[] = $this->resolve($parameter->getClass()->getName());
                }
            }
        }

        return $result;
    }

    private function findAllImplementors(string $class): array
    {
        $result = [];
        foreach ($this->itemProvider->getIds() as $id) {
            if (class_exists($id)) {
                $reflection = new \ReflectionClass($id);
                if ($reflection->implementsInterface($class) || $reflection->isSubclassOf($class)) {
                    $result[] = $this->resolve($id);
                }
            }
        }

        return $result;
    }
}
