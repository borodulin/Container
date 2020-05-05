<?php

declare(strict_types=1);

namespace Borodulin\Container;

use Borodulin\Container\Autowire\AutowireItemInterface;
use Borodulin\Container\Autowire\AutowireItemProvider;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    /**
     * @var array
     */
    private $items;
    /**
     * @var ContainerInterface
     */
    private $majorContainer;
    /**
     * @var ContainerInterface
     */
    private $minorContainer;
    /**
     * @var AutowireItemProvider
     */
    private $autowireItemProvider;

    public function __construct(array $items)
    {
        $this->items = $items;
        $this->autowireItemProvider = new AutowireItemProvider($this);
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id identifier of the entry to look for
     *
     * @return mixed entry
     *
     * @throws ContainerExceptionInterface error while retrieving the entry
     * @throws NotFoundExceptionInterface  no entry was found for **this** identifier
     */
    public function get($id)
    {
        if ($this->majorContainer) {
            if ($this->majorContainer->has($id)) {
                return $this->majorContainer->get($id);
            }
        }

        if (isset($this->items[$id])) {
            if ($this->items[$id] instanceof AutowireItemInterface) {
                $this->items[$id] = $this->autowireItemProvider->autowire($this->items[$id]);
            }

            return $this->items[$id];
        }

        if ($this->minorContainer) {
            if ($this->minorContainer->has($id)) {
                return $this->minorContainer->get($id);
            }
        }
        throw new NotFoundException($id);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id identifier of the entry to look for
     *
     * @return bool
     */
    public function has($id)
    {
        if ($this->majorContainer) {
            if ($this->majorContainer->has($id)) {
                return true;
            }
        }

        if (isset($this->items[$id])) {
            return true;
        }

        if ($this->minorContainer) {
            if ($this->minorContainer->has($id)) {
                return true;
            }
        }

        return false;
    }

    public function setMajorContainer(ContainerInterface $majorContainer): self
    {
        $this->majorContainer = $majorContainer;

        return $this;
    }

    public function setMinorContainer(ContainerInterface $minorContainer): self
    {
        $this->minorContainer = $minorContainer;

        return $this;
    }

    public function getIds(): array
    {
        return array_keys($this->items);
    }
}
