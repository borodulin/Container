<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

class InstanceFinder
{
    /**
     * @var array
     */
    private $classNames;

    public function __construct(array $classNames)
    {
        $this->classNames = $classNames;
    }

    public function findInstanceOf($interface): \Traversable
    {
        foreach ($this->classNames as $className) {
            if (class_exists($className)) {
                $reflection = new \ReflectionClass($className);
                if ($reflection->implementsInterface($interface) || $reflection->isSubclassOf($interface)) {
                    yield $className;
                }
            }
        }
    }
}
