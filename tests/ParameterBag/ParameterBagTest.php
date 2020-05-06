<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\ParameterBag;

use Borodulin\Container\Container;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\ParameterBag\Samples\Foo;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testError(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([Foo::class])
            ->build();

        $this->expectException(ContainerException::class);
        $container->get(Foo::class);
    }

    public function testParamBugAutowire(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([Foo::class])
            ->build();

        $parameterBag = new Container([
            'param1' => 1,
            'param2' => 'test',
            'param3' => [1, 2, 3],
        ]);

        $container->setParameterBag($parameterBag);

        /** @var Foo $foo */
        $foo = $container->get(Foo::class);

        $this->assertInstanceOf(Foo::class, $foo);
        $this->assertEquals(1, $foo->getParam1());
        $this->assertEquals('test', $foo->getParam2());
        $this->assertEquals([1, 2, 3], $foo->getParam3());
    }
}
