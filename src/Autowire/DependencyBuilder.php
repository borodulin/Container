<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\BuiltInTypeItem;
use Borodulin\Container\Autowire\Item\DefaultValueItem;
use Borodulin\Container\Autowire\Item\VariadicItem;
use Borodulin\Container\ContainerException;

class DependencyBuilder
{
    /**
     * @var ItemProvider
     */
    private $itemProvider;
    /**
     * @var ClassItemBuilder
     */
    private $classItemBuilder;

    public function __construct(ItemProvider $itemProvider, ClassItemBuilder $classItemBuilder)
    {
        $this->itemProvider = $itemProvider;
        $this->classItemBuilder = $classItemBuilder;
    }

    /**
     * @param \ReflectionParameter[] $parameters
     *
     * @throws ContainerException
     */
    public function buildParameters(array $parameters): array
    {
        $args = [];
        foreach ($parameters as $parameter) {
            $args[] = $this->buildParameter($parameter);
        }

        return $args;
    }

    private function resolveVariadic(\ReflectionParameter $parameter): AutowireItemInterface
    {
        if ($parameter->getClass()) {
            $variadicItem = new VariadicItem();
            $this->itemProvider->getVariadicPass()->addItem($parameter->getClass()->getName(), $variadicItem);

            return $variadicItem;
        } else {
            return new DefaultValueItem(null);
        }
    }

    private function buildParameter(\ReflectionParameter $parameter): AutowireItemInterface
    {
        if ($parameter->getType() && $parameter->getType()->isBuiltin()) {
            return new BuiltInTypeItem(
                $parameter->getName(),
                $parameter->isDefaultValueAvailable(),
                $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null
            );
        } elseif ($parameter->isVariadic()) {
            return $this->resolveVariadic($parameter);
        } elseif ($parameter->isDefaultValueAvailable()) {
            return new DefaultValueItem($parameter->getDefaultValue());
        } elseif ($parameter->getClass()) {
            return $this->classItemBuilder->build($parameter->getClass()->getName());
        }
        throw new ContainerException(sprintf('Unable to autowire parameter %s', $parameter->getName()));
    }
}
