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

    public function resolveId($id): object
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
                $args = $this->resolveParameters($constructor->getParameters());
            }
            $instance = $args ? $reflection->newInstanceArgs($args) : $reflection->newInstance();
            $this->itemProvider->setResolved($id, $instance);

            return $instance;
        } else {
            throw new NotFoundException($id);
        }
    }

    public function resolveCallableArgs(callable $callable): array
    {
        $reflection = new \ReflectionFunction(\Closure::fromCallable($callable));

        return $this->resolveParameters($reflection->getParameters());
    }

    /**
     * @param \ReflectionParameter[] $parameters
     *
     * @throws ContainerException
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    private function resolveParameters(array $parameters): array
    {
        $result = [];
        $parameterBag = $this->itemProvider->getContainer()->getParameterBag();
        foreach ($parameters as $parameter) {
            if (null !== $parameterBag) {
                if ($parameter->getType() && $parameter->getType()->isBuiltin()) {
                    if ($parameterBag->has($parameter->getName())) {
                        $result[] = $parameterBag->get($parameter->getName());
                        continue;
                    }
                }
            }
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
            } elseif ($parameter->getClass()) {
                $className = $parameter->getClass()->getName();
                if ($this->itemProvider->getContainer()->has($className)) {
                    $result[] = $this->itemProvider->getContainer()->get($className);
                } else {
                    $result[] = $this->resolveId($parameter->getClass()->getName());
                }
            } else {
                throw new ContainerException(sprintf('Unable to autowire parameter %s', $parameter->getName()));
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
                    $result[] = $this->resolveId($id);
                }
            }
        }

        return $result;
    }
}
