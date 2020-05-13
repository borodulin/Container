<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class VariadicItem implements AutowireItemInterface
{
    /**
     * @var AutowireItemInterface[]
     */
    private $args = [];

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize($this->args);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->args = unserialize($serialized);
    }

    /**
     * @return AutowireItemInterface[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param AutowireItemInterface[] $args
     */
    public function setArgs(array $args): void
    {
        $this->args = $args;
    }
}
