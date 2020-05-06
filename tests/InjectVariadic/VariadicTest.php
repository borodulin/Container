<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\InjectVariadic;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\InjectVariadic\Sample\Common;
use PHPUnit\Framework\TestCase;

class VariadicTest extends TestCase
{
    public function testVariadic(): void
    {
        $fileFinder = (new FileFinder())
            ->addPath(__DIR__.'/Sample');
        $container = (new ContainerBuilder())
            ->setFileFinder($fileFinder)
            ->build();
        $common = $container->get(Common::class);

        $this->assertCount(3, $common->getCommonInterfaces());
    }
}
