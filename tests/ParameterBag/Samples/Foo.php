<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\ParameterBag\Samples;

class Foo
{
    /**
     * @var int
     */
    private $param1;
    /**
     * @var string
     */
    private $param2;
    /**
     * @var iterable
     */
    private $param3;

    public function __construct(int $param1, string $param2, iterable $param3 = null)
    {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
    }

    public function getParam1(): int
    {
        return $this->param1;
    }

    public function getParam2(): string
    {
        return $this->param2;
    }

    public function getParam3(): iterable
    {
        return $this->param3;
    }
}
