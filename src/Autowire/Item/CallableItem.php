<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class CallableItem implements AutowireItemInterface
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var callable
     */
    private $callable;

    public function __construct(string $id, callable $callable)
    {
        $this->id = $id;
        $this->callable = $callable;
    }

    public function serialize(): string
    {
        return serialize([$this->id, \Opis\Closure\serialize($this->callable)]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [$this->id, $this->callable] = unserialize($serialized);
        $this->callable = \Opis\Closure\unserialize($this->callable);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCallable(): callable
    {
        return $this->callable;
    }
}
