<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;
use Borodulin\Container\Tests\Samples\Common\AbstractClass;
use PHPUnit\Framework\TestCase;

class ErrorsTest extends TestCase
{
    public function testAbstractClass(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples/Common');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $this->expectException(NotFoundException::class);
        $container->get(AbstractClass::class);
    }

    public function testAbstractClassConfig(): void
    {
        $this->expectException(ContainerException::class);
        (new ContainerBuilder())
            ->setConfig([
                AbstractClass::class,
            ])
            ->build();
    }

    public function testNotFoundId(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples/Common');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $this->expectException(NotFoundException::class);
        $container->get('AbstractClass::class');
    }

    public function testNotFoundClass(): void
    {
        $this->expectException(NotFoundException::class);
        (new ContainerBuilder())
            ->setConfig([
                'test.not_found' => 'not_found',
            ])
            ->build();
    }
}
