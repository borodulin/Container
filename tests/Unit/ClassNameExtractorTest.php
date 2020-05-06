<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Tests\Samples\Bar;
use Borodulin\Container\Tests\Samples\Foo;
use PHPUnit\Framework\TestCase;

class ClassNameExtractorTest extends TestCase
{
    public function testExtractor(): void
    {
        $extractor = new ClassNameExtractor();

        $this->assertEquals(Foo::class, $extractor->extract(__DIR__.'/../Samples/Foo.php'));
        $this->assertEquals(Bar::class, $extractor->extract(__DIR__.'/../Samples/Bar.php'));
    }
}
