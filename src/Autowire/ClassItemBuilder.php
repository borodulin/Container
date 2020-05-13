<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\ClassItem;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;

class ClassItemBuilder
{
    /**
     * @var ItemProvider
     */
    private $itemProvider;
    /**
     * @var array
     */
    private $buildClasses;

    public function __construct(ItemProvider $itemProvider)
    {
        $this->itemProvider = $itemProvider;
    }

    public function build(string $className): AutowireItemInterface
    {
        if ($this->itemProvider->hasItem($className)) {
            return $this->itemProvider->getItem($className);
        }
        if (class_exists($className)) {
            if (isset($this->buildClasses[$className])) {
                throw new ContainerException("$className has circular reference dependency.");
            }
            $this->buildClasses[$className] = true;
            $reflection = new \ReflectionClass($className);
            if (!$reflection->isInstantiable()) {
                throw new ContainerException("$className is not instantiable.");
            }
            $constructor = $reflection->getConstructor();
            if (null !== $constructor) {
                $args = (new DependencyBuilder($this->itemProvider, $this))
                    ->buildParameters($constructor->getParameters());
            } else {
                $args = [];
            }
            $classItem = new ClassItem($className, $args);
            $this->itemProvider->addItem($className, $classItem);
            return $classItem;
        } else {
            throw new NotFoundException($className);
        }
    }
}
