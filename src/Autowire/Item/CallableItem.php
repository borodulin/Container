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
    /**
     * @var array
     */
    private $args;

    public function __construct(callable $callable, array $args)
    {
        $this->callable = $callable;
        $this->args = $args;
    }

    public function serialize(): string
    {
        return serialize([\Opis\Closure\serialize($this->callable), $this->args]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [$this->callable, $this->args] = unserialize($serialized);
        $this->callable = \Opis\Closure\unserialize($this->callable);
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
