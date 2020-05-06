<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
use Borodulin\Container\Container;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;

class AutowireItemProvider
{
    /**
     * @var Container
     */
    private $container;

    private $resolvedItems = [];

    private $resolvingItems = [];
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;
    /**
     * @var array
     */
    private $ids;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->dependencyResolver = new DependencyResolver($this);
    }

    /**
     * @throws ContainerException
     * @throws NotFoundException
     */
    public function autowire(AutowireItemInterface $item): object
    {
        if ($item instanceof AliasItem) {
            $instance = $this->dependencyResolver->resolve($item->getAlias());
            $this->resolvedItems[$item->getId()] = $instance;

            return $instance;
        } elseif ($item instanceof CallableItem) {
            $instance = \call_user_func($item->getCallable(), $this->container);
            $this->resolvedItems[$item->getId()] = $instance;

            return $instance;
        } elseif ($item instanceof ClassItem) {
            $instance = $this->dependencyResolver->resolve($item->getClassName());
            $this->resolvedItems[$item->getClassName()] = $instance;

            return $instance;
        } else {
            throw new ContainerException('Unsupported autowire item type.');
        }
    }

    public function isResolved(string $id): bool
    {
        return isset($this->resolvedItems[$id]);
    }

    public function getResolved($id): ?object
    {
        return $this->resolvedItems[$id] ?? null;
    }

    public function setResolved(string $id, object $object): void
    {
        $this->resolvedItems[$id] = $object;
        unset($this->resolvingItems[$id]);
    }

    /**
     * @throws ContainerException
     */
    public function tryResolve(string $id): void
    {
        if (isset($this->resolvingItems[$id])) {
            throw new ContainerException("$id has recursive dependency.");
        }
        $this->resolvingItems[$id] = true;
    }

    public function getIds(): array
    {
        if (null === $this->ids) {
            $this->ids = $this->container->getIds();
        }

        return $this->ids;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }
}
