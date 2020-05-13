<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\BuiltInTypeItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
use Borodulin\Container\Autowire\Item\DefaultValueItem;
use Borodulin\Container\Autowire\Item\VariadicItem;
use Borodulin\Container\Container;
use Borodulin\Container\ContainerException;

class AutowireItemResolver
{
    /**
     * @var Container
     */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function resolve(AutowireItemInterface $item): object
    {
        if ($item instanceof AliasItem) {
            return $this->resolveAliasItem($item);
        } elseif ($item instanceof CallableItem) {
            return $this->resolveCallableItem($item);
        } elseif ($item instanceof ClassItem) {
            return $this->resolveClassItem($item);
        } else {
            throw new ContainerException('Unsupported autowire item type.');
        }
    }

    private function resolveAliasItem(AliasItem $aliasItem): object
    {
        $classItem = $aliasItem->getClassItem();
        if ($classItem instanceof ClassItem) {
            return $this->resolveClassItem($classItem);
        }

        return $this->container->get($aliasItem->getAlias());
    }

    private function resolveCallableItem(CallableItem $item)
    {
        $args = $this->resolveArgs($item->getArgs());

        return \call_user_func_array($item->getCallable(), $args);
    }

    private function resolveClassItem(ClassItem $item): object
    {
        $reflection = new \ReflectionClass($item->getClassName());
        $args = $this->resolveArgs($item->getArgs());

        return $args ? $reflection->newInstanceArgs($args) : $reflection->newInstance();
    }

    private function resolveArgs(array $args): array
    {
        $result = [];
        $parameterBug = $this->container->getParameterBag();
        foreach ($args as $arg) {
            if ($arg instanceof BuiltInTypeItem) {
                if (null !== $parameterBug && $parameterBug->has($arg->getParamName())) {
                    $result[] = $parameterBug->get($arg->getParamName());
                } elseif ($arg->isDefaultValueAvailable()) {
                    $result[] = $arg->getDefaultValue();
                } else {
                    throw new ContainerException(sprintf('Unable to autowire parameter %s', \get_class($arg)));
                }
            } elseif ($arg instanceof DefaultValueItem) {
                $result[] = $arg->getValue();
            } elseif ($arg instanceof ClassItem) {
                $result[] = $this->resolveClassItem($arg);
            } elseif ($arg instanceof AliasItem) {
                $result[] = $this->resolveAliasItem($arg);
            } elseif ($arg instanceof VariadicItem) {
                $this->resolveVariadicItem($result, $arg);
            } else {
                throw new ContainerException(sprintf('Unable to autowire parameter %s', \get_class($arg)));
            }
        }

        return $result;
    }

    private function resolveVariadicItem(array &$result, VariadicItem $item): void
    {
        if ($item->getArgs()) {
            foreach ($item->getArgs() as $arg) {
                if ($arg instanceof ClassItem) {
                    $result[] = $this->resolveClassItem($arg);
                }
            }
        } else {
            $result[] = null;
        }
    }
}
