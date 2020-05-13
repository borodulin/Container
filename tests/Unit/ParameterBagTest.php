<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Container;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\Samples\Common\BuiltInTypeArgs;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testError(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([BuiltInTypeArgs::class])
            ->build();

        $this->expectException(ContainerException::class);
        $container->get(BuiltInTypeArgs::class);
    }

    public function testParamBugAutowire(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([BuiltInTypeArgs::class])
            ->build();

        $parameterBag = new Container([
            'param1' => 1,
            'param2' => 'test',
            'param3' => [1, 2, 3],
        ]);

        $container->setParameterBag($parameterBag);

        /** @var BuiltInTypeArgs $instance */
        $instance = $container->get(BuiltInTypeArgs::class);

        $this->assertInstanceOf(BuiltInTypeArgs::class, $instance);
        $this->assertEquals(1, $instance->getParam1());
        $this->assertEquals('test', $instance->getParam2());
        $this->assertEquals([1, 2, 3], $instance->getParam3());
    }

    public function testParameterCallable(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([
                'function.alias' => function (int $paramAlias1, string $paramAlias2, iterable $paramAlias3 = null) {
                    return new BuiltInTypeArgs($paramAlias1, $paramAlias2, $paramAlias3);
                },
                BuiltInTypeArgs::class,
            ])
            ->build();

        $parameterBag = new Container([
            'paramAlias1' => 1,
            'paramAlias2' => 'test',
            'paramAlias3' => [1, 2, 3],
        ]);

        $container->setParameterBag($parameterBag);

        /** @var BuiltInTypeArgs $instance */
        $instance = $container->get('function.alias');

        $this->assertInstanceOf(BuiltInTypeArgs::class, $instance);
        $this->assertEquals(1, $instance->getParam1());
        $this->assertEquals('test', $instance->getParam2());
        $this->assertEquals([1, 2, 3], $instance->getParam3());
    }

    public function testNotFound(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([BuiltInTypeArgs::class])
            ->build();

        $parameterBag = new Container([
            'param12' => 1,
            'param2' => 'test',
            'param3' => [1, 2, 3],
        ]);

        $container->setParameterBag($parameterBag);

        $this->expectException(ContainerException::class);
        $container->get(BuiltInTypeArgs::class);
    }
}
