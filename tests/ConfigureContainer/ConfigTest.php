<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\ConfigureContainer;

use Borodulin\Container\Autowire\FileFinder;
use Borodulin\Container\ContainerBuilder;
use Borodulin\Container\Tests\ConfigureContainer\Samples\Bar;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function testConfig(): void
    {
        $fileFinder = new FileFinder();
        $builder = new ContainerBuilder($fileFinder, require(__DIR__.'/Samples/config.php'));
        $container = $builder->build();

        $bar = $container->get('test.bar');

        $this->assertInstanceOf(Bar::class, $bar);
    }
}
