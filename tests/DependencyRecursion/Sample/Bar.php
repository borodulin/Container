<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\DependencyRecursion\Sample;

class Bar
{
    /**
     * @var Foo
     */
    private $foo;

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}
