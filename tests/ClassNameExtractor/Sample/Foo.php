<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\ClassNameExtractor\Sample;

use Borodulin\Container\Tests\ClassNameExtractor\Sample\Bar\Bar;

class Foo
{
    public function __construct(Bar $bar)
    {
    }
}
