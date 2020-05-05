<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\DependencyRecursion\Sample;

class Foo
{
    /**
     * @var Bar
     */
    private $bar;

    public function __construct(Bar $bar)
    {
        $this->bar = $bar;
    }
}
