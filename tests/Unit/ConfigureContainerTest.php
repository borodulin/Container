<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\Samples\Common\Bar;
use Borodulin\Container\Tests\Samples\Common\Foo;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConfigureContainerTest extends TestCase
{
    public function testConfig(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([
                'test.bar' => Bar::class,
                'test.foo' => function (ContainerInterface $container) {
                    return new Foo($container->get('test.bar'));
                },
                Foo::class => function (Bar $bar) {
                    return new Foo($bar);
                },
            ])
            ->build();

        $foo = $container->get('test.foo');
        $this->assertInstanceOf(Foo::class, $foo);

        $foo = $container->get(Foo::class);
        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testError(): void
    {
        $this->expectException(ContainerException::class);
        (new ContainerBuilder())
            ->setConfig([
                'test.int' => 1,
            ])
            ->build();
    }
}
