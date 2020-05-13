<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\BuiltInTypeItem;
use Borodulin\Container\Autowire\Item\DefaultValueItem;
use Borodulin\Container\Autowire\Item\VariadicItem;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;

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
     * @throws NotFoundException
     * @throws \ReflectionException
     */
    public function buildParameters(array $parameters): array
    {
        $args = [];
        foreach ($parameters as $parameter) {
            if ($parameter->getType() && $parameter->getType()->isBuiltin()) {
                $args[] = new BuiltInTypeItem(
                    $parameter->getName(),
                    $parameter->isDefaultValueAvailable(),
                    $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null
                );
            } elseif ($parameter->isVariadic()) {
                $args[] = $this->resolveVariadic($parameter);
            } elseif ($parameter->isDefaultValueAvailable()) {
                $args[] = new DefaultValueItem($parameter->getDefaultValue());
            } elseif ($parameter->getClass()) {
                $args[] = $this->classItemBuilder->build($parameter->getClass()->getName());
            } else {
                throw new ContainerException(sprintf('Unable to autowire parameter %s', $parameter->getName()));
            }
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
}
