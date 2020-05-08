<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Container;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;
use Psr\Container\ContainerInterface;

class DependencyResolver
{
    /**
     * @var AutowireItemProvider
     */
    private $itemProvider;
    /**
     * @var Container
     */
    private $container;

    public function __construct(AutowireItemProvider $itemProvider)
    {
        $this->itemProvider = $itemProvider;
        $this->container = $itemProvider->getContainer();
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
     * @throws \ReflectionException
     */
    private function resolveParameters(array $parameters): array
    {
        $args = [];
        $parameterBug = $this->container->getParameterBag();
        foreach ($parameters as $parameter) {
            if (null !== $parameterBug) {
                if ($this->resolveParameterBag($args, $parameter, $parameterBug)) {
                    continue;
                }
            }

            if ($parameter->isVariadic()) {
                $this->resolveVariadic($args, $parameter);
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = $parameter->getDefaultValue();
            } elseif ($parameter->getClass()) {
                $args[] = $this->resolveClass($parameter->getClass()->getName());
            } else {
                throw new ContainerException(sprintf('Unable to autowire parameter %s', $parameter->getName()));
            }
        }

        return $args;
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

    private function resolveParameterBag(array &$args, \ReflectionParameter $parameter, ContainerInterface $parameterBag): bool
    {
        if ($parameter->getType() && $parameter->getType()->isBuiltin()) {
            if ($parameterBag->has($parameter->getName())) {
                $args[] = $parameterBag->get($parameter->getName());

                return true;
            }
        }

        return false;
    }

    private function resolveVariadic(array &$args, \ReflectionParameter $parameter): void
    {
        if ($parameter->getClass()) {
            foreach ($this->findAllImplementors($parameter->getClass()->getName()) as $implementor) {
                $args[] = $implementor;
            }
        } else {
            $args[] = null;
        }
    }

    private function resolveClass(string $className): object
    {
        if ($this->container->has($className)) {
            return $this->container->get($className);
        } else {
            return $this->resolveId($className);
        }
    }
}
