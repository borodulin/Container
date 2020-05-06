<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Container;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\Samples\BuiltInTypeArgs;
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
}
