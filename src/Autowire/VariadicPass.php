<?php

declare(strict_types=1);

namespace Borodulin\Container\Autowire;

use Borodulin\Container\Autowire\Item\VariadicItem;

class VariadicPass
{
    private $items = [];

    public function addItem(string $interface, VariadicItem $item): void
    {
        $this->items[$interface][] = $item;
    }

    public function getItems(): array
    {
        return $this->items;
    }
}
