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

    public function __construct(string $alias)
    {
        $this->alias = $alias;
    }

    public function serialize(): string
    {
        return serialize($this->alias);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $this->alias = unserialize($serialized);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
