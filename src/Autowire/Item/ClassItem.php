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

    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function serialize()
    {
        return $this->className;
    }

    public function unserialize($serialized): void
    {
        $this->className = $serialized;
    }

    public function getClassName(): string
    {
        return $this->className;
    }
}
