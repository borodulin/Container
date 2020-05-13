<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\ContainerException;
use PHPUnit\Framework\TestCase;

class CircleReferenceTest extends TestCase
{
    public function testCircleReference(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/../Samples/CircleReference');
        $this->expectException(ContainerException::class);
        (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
    }
}
