<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\CallableItem;

class CallableItemBuilder
{
    /**
     * @var ItemProvider
     */
    private $itemProvider;

    public function __construct(ItemProvider $itemProvider)
    {
        $this->itemProvider = $itemProvider;
    }

    public function build(callable $callable): CallableItem
    {
        $reflection = new \ReflectionFunction(\Closure::fromCallable($callable));
        $args = (new DependencyBuilder($this->itemProvider, new ClassItemBuilder($this->itemProvider)))
            ->buildParameters($reflection->getParameters());

        return new CallableItem($callable, $args);
    }
}
