<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
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
        (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->setCache($cache)
            ->build();
        $this->assertIsArray($this->cache);
        $this->assertEquals(ContainerBuilder::class, $this->cache[0]);

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

        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->setCache($cache)
            ->build();

        $this->assertIsArray($container->getIds());
    }
}
