<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Samples\CircleReference;

class Hen
{
    /**
     * @var Egg
     */
    private $egg;

    public function __construct(Egg $egg)
    {
        $this->egg = $egg;
    }
}
