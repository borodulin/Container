<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use PHPUnit\Framework\TestCase;

class CacheTest extends TestCase
{
    public function testCache(): void
    {
        $this->assertCount(0, []);
    }
}
