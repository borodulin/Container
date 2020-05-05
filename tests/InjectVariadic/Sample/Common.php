<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\InjectVariadic\Sample;

class Common
{
    /**
     * @var CommonInterface[]
     */
    private $commonInterfaces;

    public function __construct(CommonInterface ...$commonInterfaces)
    {
        $this->commonInterfaces = $commonInterfaces;
    }

    /**
     * @return CommonInterface[]
     */
    public function getCommonInterfaces(): array
    {
        return $this->commonInterfaces;
    }
}
