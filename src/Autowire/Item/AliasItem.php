<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class AliasItem implements AutowireItemInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $alias;

    public function __construct(string $id, string $alias)
    {
        $this->id = $id;
        $this->alias = $alias;
    }

    public function serialize(): string
    {
        return serialize([$this->id, $this->alias]);
    }

    public function unserialize($serialized): void
    {
        [$this->id, $this->alias] = unserialize($serialized);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }
}
