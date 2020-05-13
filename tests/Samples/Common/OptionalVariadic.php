<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Samples\Common;

class OptionalVariadic
{
    /**
     * @var UnusedInterface[]
     */
    private $unusedInterfaces;

    public function __construct(UnusedInterface ...$unusedInterfaces)
    {
        $this->unusedInterfaces = $unusedInterfaces;
    }
}
