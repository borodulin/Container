<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire\Item;

use Borodulin\Container\Autowire\AutowireItemInterface;

class BuiltInTypeItem implements AutowireItemInterface
{
    /**
     * @var string
     */
    private $paramName;
    /**
     * @var mixed
     */
    private $defaultValue;
    /**
     * @var bool
     */
    private $isDefaultValueAvailable;

    public function __construct(string $paramName, bool $isDefaultValueAvailable, $defaultValue)
    {
        $this->paramName = $paramName;
        $this->isDefaultValueAvailable = $isDefaultValueAvailable;
        $this->defaultValue = $defaultValue;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize(): string
    {
        return serialize([$this->paramName, $this->isDefaultValueAvailable, $this->defaultValue]);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized): void
    {
        [$this->paramName, $this->isDefaultValueAvailable, $this->defaultValue] = unserialize($serialized);
    }

    public function getParamName(): string
    {
        return $this->paramName;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isDefaultValueAvailable(): bool
    {
        return $this->isDefaultValueAvailable;
    }
}
