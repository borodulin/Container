<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\ContainerException;

class ResolverItemProvider
{
    /**
     * @var array
     */
    private $resolvedItems = [];
    /**
     * @var array
     */
    private $resolvingItems = [];

    public function isResolved(string $id): bool
    {
        return isset($this->resolvedItems[$id]);
    }

    public function getResolved($id): object
    {
        return $this->resolvedItems[$id];
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
            throw new ContainerException("$id has circular reference dependency.");
        }
        $this->resolvingItems[$id] = true;
    }
}
