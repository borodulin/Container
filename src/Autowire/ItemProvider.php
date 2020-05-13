<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\ClassItem;

class ItemProvider implements \IteratorAggregate, \Serializable
{
    /**
     * @var array
     */
    private $items = [];
    /**
     * @var VariadicPass
     */
    private $variadicPass;

    public function __construct()
    {
        $this->variadicPass = new VariadicPass();
    }

    public function addItem(string $id, AutowireItemInterface $item): void
    {
        $this->items[$id] = $item;
    }

    public function hasItem(string $id): bool
    {
        return isset($this->items[$id]);
    }

    public function getItem(string $id): AutowireItemInterface
    {
        return $this->items[$id];
    }

    public function findInstanceOf($interface): \Traversable
    {
        foreach ($this->items as $id => $item) {
            if ($item instanceof ClassItem && class_exists($item->getClassName())) {
                $className = $item->getClassName();
            } elseif ($item instanceof AliasItem && class_exists($item->getAlias())) {
                $className = $item->getAlias();
            } else {
                continue;
            }
            $reflection = new \ReflectionClass($className);
            if (
                $reflection->isInstantiable()
                && ($reflection->implementsInterface($interface) || $reflection->isSubclassOf($interface))
            ) {
                yield $id;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->items = unserialize($serialized);
    }

    public function getVariadicPass(): VariadicPass
    {
        return $this->variadicPass;
    }
}
