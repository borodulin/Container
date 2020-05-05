<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\ClassNameExtractor;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Tests\ClassNameExtractor\Sample\Bar\Bar;
use Borodulin\Container\Tests\ClassNameExtractor\Sample\Foo;
use PHPUnit\Framework\TestCase;

class ClassNameExtractorTest extends TestCase
{
    public function testExtractor(): void
    {
        $extractor = new ClassNameExtractor();

        $this->assertEquals(Foo::class, $extractor->extract(__DIR__.'/Sample/Foo.php'));
        $this->assertEquals(Bar::class, $extractor->extract(__DIR__.'/Sample/Bar/Bar.php'));
    }
}
