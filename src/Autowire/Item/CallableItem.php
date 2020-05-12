<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class CallableItem implements AutowireItemInterface
{
    /**
     * @var callable
     */
    private $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function serialize(): string
    {
        return \Opis\Closure\serialize($this->callable);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        $this->callable = \Opis\Closure\unserialize($serialized);
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }
}
