<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\DependencyRecursion;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\DependencyRecursion\Sample\Foo;
use PHPUnit\Framework\TestCase;

class RecursionTest extends TestCase
{
    public function testRecursion(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/Sample');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();

        $this->expectException(ContainerException::class);
        $container->get(Foo::class);
    }
}
