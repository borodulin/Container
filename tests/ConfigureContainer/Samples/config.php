<?php

declare(strict_types=1);

use Borodulin\Container\Tests\ConfigureContainer\Samples\Bar;
use Borodulin\Container\Tests\ConfigureContainer\Samples\Foo;
use Psr\Container\ContainerInterface;

return [
    'test.foo' => Foo::class,
    'test.bar' => function (ContainerInterface $container) {
        return new Bar($container->get('test.foo'));
    },
];
