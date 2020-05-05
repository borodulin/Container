<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\MiddlewareContainer;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\MiddlewareContainer\Samples\Bar;
use Borodulin\Container\Tests\MiddlewareContainer\Samples\Foo;
use PHPUnit\Framework\TestCase;

class MiddlewareContainerTest extends TestCase
{
    public function testMajor(): void
    {
        $container1 = (new ContainerBuilder(null, [
            Bar::class,
        ]))->build();

        $container2 = (new ContainerBuilder(null, [
            Foo::class,
        ]))->build()->setMajorContainer($container1);

        $foo = $container2->get(Foo::class);
        $this->assertInstanceOf(Foo::class, $foo);

        $bar = $container2->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);
    }

    public function testMinor(): void
    {
        $container1 = (new ContainerBuilder(null, [
            Bar::class,
        ]))->build();

        $container2 = (new ContainerBuilder(null, [
            Foo::class,
        ]))->build()->setMinorContainer($container1);

        $foo = $container2->get(Foo::class);
        $this->assertInstanceOf(Foo::class, $foo);

        $bar = $container2->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $bar);
    }
}
