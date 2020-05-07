<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\Samples\OptionalParam;
use Borodulin\Container\Tests\Samples\Palette;
use PHPUnit\Framework\TestCase;

class AutowireTest extends TestCase
{
    public function testVariadic(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();

        /** @var Palette $palette */
        $palette = $container->get(Palette::class);

        $this->assertCount(3, $palette->getColors());
    }

    public function testOptional(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples');

        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $instance = $container->get(OptionalParam::class);
        $this->assertInstanceOf(OptionalParam::class, $instance);
    }
}
