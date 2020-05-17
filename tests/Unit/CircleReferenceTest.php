<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use Borodulin\Finder\ClassFinder;
use PHPUnit\Framework\TestCase;

class CircleReferenceTest extends TestCase
{
    public function testCircleReference(): void
    {
        $fileFinder = (new ClassFinder())
            ->addPath(__DIR__.'/../Samples/CircleReference');
        $this->expectException(ContainerException::class);
        (new ContainerBuilder())
            ->setClassFinder($fileFinder)
            ->build();
    }
}
