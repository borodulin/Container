<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Samples;

class Palette
{
    /**
     * @var ColorInterface[]
     */
    private $colors;

    public function __construct(ColorInterface ...$colors)
    {
        $this->colors = $colors;
    }

    /**
     * @return ColorInterface[]
     */
    public function getColors(): array
    {
        return $this->colors;
    }
}
