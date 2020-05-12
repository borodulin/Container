<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\Tests\Samples\Bar;
use Borodulin\Container\Tests\Samples\Foo;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;

class CacheTest extends TestCase
{
    private $cache;

    public function testCache(): void
    {
        $cache = $this->createMock(CacheInterface::class);
        $cache
            ->method('set')
            ->willReturnCallback(function ($name, $value): void {
                $this->cache = [$name, $value];
            })
        ;
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples');
        $builder = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->setConfig([
                'test.closure' => function (Bar $bar) {
                    return new Foo($bar);
                },
                'test.alias' => 'test.closure',
            ])
            ->setCache($cache);
        $builder->build();
        $this->assertIsArray($this->cache);

        $cache
            ->method('get')
            ->willReturn($this->cache[1])
        ;
        $cache
            ->method('has')
            ->willReturn(true)
        ;
        $cache
            ->method('set')
            ->willThrowException(new ContainerException())
        ;

        $container = $builder->build();

        $this->assertIsArray($container->getIds());
    }
}
