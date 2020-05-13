<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Samples\Common;

class OptionalParam
{
    /**
     * @var int
     */
    private $int;
    /**
     * @var string|null
     */
    private $string;
    /**
     * @var array
     */
    private $optional;
    /**
     * @var Bar|null
     */
    private $bar;

    public function __construct(int $int = 0, string $string = null, Bar $bar = null, ...$optional)
    {
        $this->int = $int;
        $this->string = $string;
        $this->optional = $optional;
        $this->bar = $bar;
    }
}
