<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Container\NotFoundException;
use Borodulin\Container\Tests\Samples\Errors\AbstractClass;
use PHPUnit\Framework\TestCase;

class ErrorsTest extends TestCase
{
    public function testAbstractClass(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $this->expectException(ContainerException::class);
        $container->get(AbstractClass::class);
    }

    public function testNotFoundId(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $this->expectException(NotFoundException::class);
        $container->get('AbstractClass::class');
    }

    public function testNotFoundClass(): void
    {
        $container = (new ContainerBuilder())
            ->setConfig([
                'test.not_found' => 'not_found',
            ])
            ->build();
        $this->expectException(NotFoundException::class);
        $container->get('test.not_found');
    }
}
