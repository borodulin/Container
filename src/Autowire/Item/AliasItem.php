<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class AliasItem implements AutowireItemInterface
{
    /**
     * @var string
     */
    private $alias;
    /**
     * @var AutowireItemInterface|null
     */
    private $classItem;

    public function __construct(string $alias, AutowireItemInterface $classItem = null)
    {
        $this->alias = $alias;
        $this->classItem = $classItem;
    }

    public function serialize(): string
    {
        return serialize([$this->alias, $this->classItem]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [$this->alias, $this->classItem] = unserialize($serialized);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getClassItem(): ?AutowireItemInterface
    {
        return $this->classItem;
    }
}
