<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class DefaultValueItem implements AutowireItemInterface
{
    /**
     * @var mixed
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize($this->value);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        $this->value = unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
