<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\AliasItem;
use Borodulin\Container\Autowire\Item\CallableItem;
use Borodulin\Container\Autowire\Item\ClassItem;
use Borodulin\Container\ContainerException;

class AutowireItemBuilder
{
    /**
     * @var DependencyResolver
     */
    private $dependencyResolver;

    public function __construct(DependencyResolver $dependencyResolver)
    {
        $this->dependencyResolver = $dependencyResolver;
    }

    public function build(AutowireItemInterface $autowireItem): object
    {
        if ($autowireItem instanceof AliasItem) {
            return $this->dependencyResolver->resolveId($autowireItem->getAlias());
        } elseif ($autowireItem instanceof CallableItem) {
            $args = $this->dependencyResolver->resolveCallableArgs($autowireItem->getCallable());

            return \call_user_func_array($autowireItem->getCallable(), $args);
        } elseif ($autowireItem instanceof ClassItem) {
            return $this->dependencyResolver->resolveId($autowireItem->getClassName());
        } else {
            throw new ContainerException('Unsupported autowire item type.');
        }
    }
}
