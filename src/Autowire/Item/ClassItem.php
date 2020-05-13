<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class ClassItem implements AutowireItemInterface
{
    /**
     * @var string
     */
    private $className;
    /**
     * @var array
     */
    private $args;

    public function __construct(string $className, array $args)
    {
        $this->className = $className;
        $this->args = $args;
    }

    public function serialize(): string
    {
        return serialize([$this->className, $this->args]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [$this->className, $this->args] = unserialize($serialized);
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}
