<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Samples\CircleReference;

class Egg
{
    /**
     * @var Hen
     */
    private $hen;

    public function __construct(Hen $hen)
    {
        $this->hen = $hen;
    }
}
