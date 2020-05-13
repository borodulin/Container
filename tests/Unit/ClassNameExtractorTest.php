<?php

declare(strict_types=1);

namespace Borodulin\Container\Tests\Unit;

use Borodulin\Container\Autowire\ClassNameExtractor;
use Borodulin\Container\Tests\Samples\Common\Bar;
use Borodulin\Container\Tests\Samples\Common\Foo;
use PHPUnit\Framework\TestCase;

class ClassNameExtractorTest extends TestCase
{
    public function testExtractor(): void
    {
        $extractor = new ClassNameExtractor();

        $this->assertEquals(Foo::class, $extractor->extract(__DIR__.'/../Samples/Common/Foo.php'));
        $this->assertEquals(Bar::class, $extractor->extract(__DIR__.'/../Samples/Common/Bar.php'));
        $this->assertNull($extractor->extract(__DIR__.'/../Samples/Common/AbstractClass.php'));
    }
}
